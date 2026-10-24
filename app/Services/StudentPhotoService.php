<?php

namespace App\Services;

use App\Models\StorageFile;
use App\Models\Student;
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
 * Lokasi di bucket: profile-{nim}/formal.{ext} dan profile-{nim}/casual.{ext}
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
        $staged = [];

        foreach (array_keys(self::TYPES) as $type) {
            $field = "{$type}_photo_upload";
            $path = Arr::first(Arr::wrap($data[$field] ?? null));
            unset($data[$field]);

            if (filled($path)) {
                $staged[$type] = $path;
            }
        }

        if ($staged === []) {
            return $data;
        }

        $this->assertPublicUrlConfigured();

        $nim = $this->cleanNim($data['nim'] ?? $student?->nim);
        $uploadedBy = filament()->auth()->user()?->email;

        $newRecords = [];
        $newKeys = [];

        try {
            foreach ($staged as $type => $path) {
                $result = $this->uploadOne($path, $type, $nim, $uploadedBy);

                $data[self::TYPES[$type]] = $result['url'];
                $newKeys[$type] = $result['key'];

                if ($result['is_new']) {
                    $newRecords[] = $result['record'];
                }
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
        } finally {
            foreach ($staged as $path) {
                Storage::disk('local')->delete($path);
            }
        }

        // Hapus foto lama hanya jika lokasinya berbeda (mis. png diganti jpg)
        if ($student) {
            foreach ($newKeys as $type => $newKey) {
                $oldUrl = $student->{self::TYPES[$type]};

                if ($this->keyFromUrl($oldUrl) !== $newKey) {
                    try {
                        $this->deleteByUrl($oldUrl);
                    } catch (Throwable $exception) {
                        report($exception);
                    }
                }
            }
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
        $key = "profile-{$nim}/{$type}.{$extension}";

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
        if (! Storage::disk(self::DISK)->exists($key)) {
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
     * https://cdn.x.com/profile-21000123/formal.jpg?v=1 => profile-21000123/formal.jpg
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
