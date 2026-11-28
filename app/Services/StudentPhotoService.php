<?php

namespace App\Services;

use App\Models\StorageFile;
use App\Models\Student;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 * Menjembatani form StudentResource dengan R2FileManagerService.
 *
 * Alur: file singgah di disk lokal (tmp-uploads) -> dikirim ke R2 lewat
 * R2FileManagerService (jadi tercatat di tabel storage_files) -> URL publik
 * disimpan ke kolom formal_photo_url / casual_photo_url.
 *
 * Lokasi di bucket: students/{nim}/formal.{ext} dan students/{nim}/casual.{ext}
 */
class StudentPhotoService
{
    /** tipe foto => kolom database */
    public const TYPES = [
        'formal' => 'formal_photo_url',
        'casual' => 'casual_photo_url',
    ];

    protected const DISK = 'r2';

    public function __construct(protected R2FileManagerService $files) {}

    /**
     * Dipanggil dari halaman Create/Edit. Mengambil field upload virtual
     * ({type}_photo_upload) dari $data, mengunggahnya, lalu mengisi kolom URL.
     */
    public function processFormData(array $data, ?Student $student = null): array
    {
        $local = Storage::disk('local');
        $staged = [];
        $pathsToCleanup = [];

        foreach (array_keys(self::TYPES) as $type) {
            $field = "{$type}_photo_upload";
            $candidates = array_values(array_filter(Arr::wrap($data[$field] ?? null), 'filled'));
            unset($data[$field]);

            if ($candidates === []) {
                continue;
            }

            $pathsToCleanup = array_merge($pathsToCleanup, $candidates);
            $staged[$type] = $this->resolveStagedPath($candidates, $local);
        }

        if ($staged === []) {
            return $data;
        }

        $this->assertPublicUrlConfigured();

        $nim = $this->cleanNim($data['nim'] ?? $student?->nim);
        $uploadedBy = filament()->auth()->user()?->email;

        foreach ($staged as $path) {
            if (! $local->exists($path)) {
                throw new RuntimeException("File sementara tidak ditemukan: {$path}");
            }
        }

        $newRecords = [];

        try {
            foreach ($staged as $type => $path) {
                if ($student) {
                    try {
                        $this->deleteByUrl($student->{self::TYPES[$type]});
                    } catch (Throwable $exception) {
                        report($exception);
                    }
                }

                $result = $this->uploadOne($path, $type, $nim, $uploadedBy);

                $data[self::TYPES[$type]] = $result['url'];

                if ($result['is_new']) {
                    $newRecords[] = $result['record'];
                }
            }

            foreach ($pathsToCleanup as $path) {
                $local->delete($path);
            }
        } catch (Throwable $exception) {
            // Batalkan upload yang sudah terlanjur masuk supaya tidak jadi file yatim
            foreach ($newRecords as $record) {
                try {
                    $this->files->delete($record);
                } catch (Throwable $cleanup) {
                    report($cleanup);
                }
            }

            throw $exception;
        }

        return $data;
    }

    /**
     * Hapus semua foto milik mahasiswa (dipanggil saat mahasiswa dihapus).
     */
    public function deletePhotos(Student $student): void
    {
        foreach (self::TYPES as $column) {
            $this->deleteByUrl($student->{$column});
        }
    }

    /**
     * Hapus file berdasarkan URL publiknya. Jika file tercatat di
     * storage_files, hapus lewat service; jika tidak (mis. hasil seeding
     * lama), hapus langsung dari R2.
     */
    public function deleteByUrl(?string $url): void
    {
        $key = $this->keyFromUrl($url);

        if ($key === null) {
            return;
        }

        $record = StorageFile::query()
            ->where('disk', self::DISK)
            ->where('path', $key)
            ->first();

        if ($record) {
            $this->files->delete($record);

            return;
        }

        Storage::disk(self::DISK)->delete($key);
    }

    /**
     * @return array{url: string, key: string, record: StorageFile, is_new: bool}
     */
    protected function uploadOne(string $stagedPath, string $type, string $nim, ?string $uploadedBy): array
    {
        $local = Storage::disk('local');

        if (! $local->exists($stagedPath)) {
            throw new RuntimeException("File sementara tidak ditemukan: {$stagedPath}");
        }

        // Ekstensi harus selalu ada, kalau tidak R2FileManagerService menganggapnya folder
        $extension = strtolower(pathinfo($stagedPath, PATHINFO_EXTENSION)) ?: 'jpg';
        $key = "students/{$nim}/{$type}.{$extension}";

        $existed = StorageFile::query()
            ->where('disk', self::DISK)
            ->where('path', $key)
            ->exists();

        $file = new UploadedFile(
            $local->path($stagedPath),
            "{$type}.{$extension}",
            $local->mimeType($stagedPath),
            null,
            true,
        );

        $record = $this->files->upload(
            file: $file,
            desiredPath: $key,
            visibility: StorageFile::VISIBILITY_PUBLIC,
            uploadedBy: $uploadedBy,
            overwrite: true,
        );

        // Disk r2 memakai 'throw' => false, jadi put() yang gagal tidak melempar
        // error. Pastikan file benar-benar ada di bucket.
        try {
            $existsOnBucket = Storage::disk(self::DISK)->exists($key);
        } catch (Throwable $exception) {
            if (! $existed) {
                $record->delete();
            }

            throw new RuntimeException(
                'Gagal memverifikasi file di R2: '.$exception->getMessage(),
                0,
                $exception,
            );
        }

        if (! $existsOnBucket) {
            if (! $existed) {
                $record->delete();
            }

            throw new RuntimeException(
                'File tidak ditemukan di R2 setelah upload. Periksa kredensial, endpoint, dan pengaturan ACL/visibility bucket.'
            );
        }

        // ?v= mencegah browser/CDN menampilkan foto lama saat file ditimpa di path yang sama
        $url = Storage::disk(self::DISK)->url($key).'?v='.time();

        return ['url' => $url, 'key' => $key, 'record' => $record, 'is_new' => ! $existed];
    }

    /**
     * Ubah URL publik menjadi object key, mis.
     * https://cdn.x.com/students/21000123/formal.jpg?v=1 => students/21000123/formal.jpg
     */
    protected function keyFromUrl(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $base = rtrim((string) config('filesystems.disks.'.self::DISK.'.url'), '/');

        if ($base === '' || ! str_starts_with($url, $base.'/')) {
            return null;
        }

        $rest = substr($url, strlen($base) + 1);
        $rest = Str::before(Str::before($rest, '?'), '#');

        return $rest === '' ? null : urldecode($rest);
    }

    /**
     * Pilih path file sementara terbaru yang benar-benar ada di disk lokal,
     * sehingga sisa state FileUpload yang sudah terhapus tidak memblokir upload baru.
     *
     * @param  array<int, string>  $candidates
     */
    protected function resolveStagedPath(array $candidates, Filesystem $local): string
    {
        foreach (array_reverse($candidates) as $candidate) {
            if ($local->exists($candidate)) {
                return $candidate;
            }
        }

        return $candidates[array_key_last($candidates)];
    }

    protected function cleanNim(mixed $nim): string
    {
        $clean = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $nim);

        if ($clean === '' || $clean === null) {
            throw new InvalidArgumentException('NIM wajib diisi sebelum mengunggah foto.');
        }

        return $clean;
    }

    protected function assertPublicUrlConfigured(): void
    {
        if (blank(config('filesystems.disks.'.self::DISK.'.url'))) {
            throw new RuntimeException('R2_PUBLIC_DOMAIN belum diisi di .env, URL publik foto tidak bisa dibuat.');
        }
    }
}
