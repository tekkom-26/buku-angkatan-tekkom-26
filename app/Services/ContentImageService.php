<?php

namespace App\Services;

use App\Models\StorageFile;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Menjembatani form CMS Filament (ManageIdentityContent & ManageLandingContent)
 * dengan R2FileManagerService untuk unggah gambar ke Cloudflare R2.
 *
 * Alur: file singgah di disk lokal (tmp-uploads) -> diunggah ke R2 lewat
 * R2FileManagerService (tercatat di tabel storage_files dengan visibility public)
 * -> URL publik CDN ditulis ke field target JSON pada app_config.
 */
class ContentImageService
{
    protected const DISK = 'r2';

    protected const IDENTITY_PREFIX = 'content/identity';

    protected const LANDING_PREFIX = 'content/landing';

    public function __construct(protected R2FileManagerService $files) {}

    /**
     * Proses seluruh unggahan gambar pada form Konten Identitas.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $existingContent
     * @return array<string, mixed>
     */
    public function processIdentityFormData(array $data, array $existingContent = []): array
    {
        foreach (['vision_mission.missions', 'color_pillars.pillars', 'logo_symbolism.slides', 'mascot.slides'] as $listPath) {
            $list = Arr::get($data, $listPath);
            if (is_array($list)) {
                Arr::set($data, $listPath, array_values($list));
            }
        }

        $uploadSpecs = [
            [
                'upload_path' => 'header_banner.banner_upload',
                'target_path' => 'header_banner.banner_placeholder',
                'directory' => self::IDENTITY_PREFIX,
                'slug' => 'header-banner',
            ],
        ];

        foreach (['logo_symbolism' => 'logo', 'mascot' => 'mascot'] as $section => $subDir) {
            $slides = Arr::get($data, "{$section}.slides");
            if (! is_array($slides)) {
                continue;
            }

            foreach ($slides as $index => $slide) {
                if (! is_array($slide)) {
                    continue;
                }

                $label = (string) ($slide['part'] ?? $slide['title'] ?? 'slide-'.($index + 1));

                $uploadSpecs[] = [
                    'upload_path' => "{$section}.slides.{$index}.image_upload",
                    'target_path' => "{$section}.slides.{$index}.image",
                    'directory' => self::IDENTITY_PREFIX.'/'.$subDir,
                    'slug' => $label,
                ];
            }
        }

        return $this->processUploadSpecs($data, $existingContent, $uploadSpecs, self::IDENTITY_PREFIX);
    }

    /**
     * Proses seluruh unggahan gambar pada form Konten Beranda.
     *
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $existingContent
     * @return array<string, mixed>
     */
    public function processLandingFormData(array $data, array $existingContent = []): array
    {
        $moments = Arr::get($data, 'moments');
        if (is_array($moments)) {
            $moments = array_values($moments);
            Arr::set($data, 'moments', $moments);
        }

        $uploadSpecs = [
            [
                'upload_path' => 'hero.mascot_upload',
                'target_path' => 'hero.mascot_placeholder',
                'directory' => self::LANDING_PREFIX,
                'slug' => 'hero-mascot',
            ],
            [
                'upload_path' => 'decoding.logo_upload',
                'target_path' => 'decoding.logo_placeholder',
                'directory' => self::LANDING_PREFIX,
                'slug' => 'decoding-logo',
            ],
        ];

        if (is_array($moments)) {
            foreach ($moments as $index => $moment) {
                if (! is_array($moment)) {
                    continue;
                }

                $label = (string) ($moment['title'] ?? $moment['categoryLabel'] ?? 'moment-'.($index + 1));

                $uploadSpecs[] = [
                    'upload_path' => "moments.{$index}.image_upload",
                    'target_path' => "moments.{$index}.image",
                    'directory' => self::LANDING_PREFIX.'/moments',
                    'slug' => $label,
                ];
            }
        }

        return $this->processUploadSpecs($data, $existingContent, $uploadSpecs, self::LANDING_PREFIX);
    }

    /**
     * Resolusi URL pratinjau yang aman untuk ditampilkan pada ImageEntry Filament.
     */
    public function previewUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $trimmed = trim($path);

        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
            return $trimmed;
        }

        if (str_starts_with($trimmed, '/')) {
            return asset($trimmed);
        }

        return null;
    }

    /**
     * Hapus file R2 berdasarkan URL publiknya jika berada di dalam prefix yang diizinkan.
     */
    public function deleteByUrl(?string $url, string $allowedPrefix): void
    {
        $key = $this->keyFromUrl($url);

        if ($key === null || ! str_starts_with($key, rtrim($allowedPrefix, '/').'/')) {
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
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $existingContent
     * @param  array<int, array{upload_path: string, target_path: string, directory: string, slug: string}>  $specs
     * @return array<string, mixed>
     */
    protected function processUploadSpecs(
        array $data,
        array $existingContent,
        array $specs,
        string $cleanupPrefix,
    ): array {
        $local = Storage::disk('local');
        $staged = [];
        $pathsToCleanup = [];
        $replacedUrls = [];

        foreach ($specs as $spec) {
            $rawUpload = Arr::get($data, $spec['upload_path']);
            Arr::forget($data, $spec['upload_path']);

            $candidates = array_values(array_filter(Arr::wrap($rawUpload), 'filled'));

            if ($candidates === []) {
                continue;
            }

            $pathsToCleanup = array_merge($pathsToCleanup, $candidates);
            $resolvedPath = $this->resolveStagedPath($candidates, $local);

            $previousUrl = Arr::get($data, $spec['target_path'])
                ?? Arr::get($existingContent, $spec['target_path']);

            if (is_string($previousUrl) && $previousUrl !== '') {
                $replacedUrls[] = $previousUrl;
            }

            $staged[] = [
                'staged_path' => $resolvedPath,
                'target_path' => $spec['target_path'],
                'directory' => $spec['directory'],
                'slug' => $spec['slug'],
            ];
        }

        if ($staged === []) {
            return $data;
        }

        $this->assertPublicUrlConfigured();

        foreach ($staged as $item) {
            if (! $local->exists($item['staged_path'])) {
                throw new RuntimeException("File sementara tidak ditemukan: {$item['staged_path']}");
            }
        }

        $uploadedBy = filament()->auth()->user()?->email;
        $newRecords = [];

        try {
            foreach ($staged as $item) {
                $result = $this->uploadOne(
                    stagedPath: $item['staged_path'],
                    directory: $item['directory'],
                    slug: $item['slug'],
                    uploadedBy: $uploadedBy,
                );

                Arr::set($data, $item['target_path'], $result['url']);

                if ($result['is_new']) {
                    $newRecords[] = $result['record'];
                }
            }

            foreach ($pathsToCleanup as $path) {
                $local->delete($path);
            }
        } catch (Throwable $exception) {
            foreach ($newRecords as $record) {
                try {
                    $this->files->delete($record);
                } catch (Throwable $cleanup) {
                    report($cleanup);
                }
            }

            throw $exception;
        }

        $activeUrls = $this->collectStringValues($data);

        foreach (array_unique($replacedUrls) as $oldUrl) {
            if (in_array($oldUrl, $activeUrls, true)) {
                continue;
            }

            try {
                $this->deleteByUrl($oldUrl, $cleanupPrefix);
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        return $data;
    }

    /**
     * @return array{url: string, key: string, record: StorageFile, is_new: bool}
     */
    protected function uploadOne(string $stagedPath, string $directory, string $slug, ?string $uploadedBy): array
    {
        $local = Storage::disk('local');

        if (! $local->exists($stagedPath)) {
            throw new RuntimeException("File sementara tidak ditemukan: {$stagedPath}");
        }

        $extension = strtolower(pathinfo($stagedPath, PATHINFO_EXTENSION)) ?: 'png';
        $safeSlug = Str::slug($slug) ?: 'asset';
        $filename = $safeSlug.'-'.Str::lower(Str::random(8)).'.'.$extension;
        $key = trim($directory, '/').'/'.$filename;

        $existed = StorageFile::query()
            ->where('disk', self::DISK)
            ->where('path', $key)
            ->exists();

        $file = new UploadedFile(
            $local->path($stagedPath),
            $filename,
            $local->mimeType($stagedPath) ?: 'image/png',
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
                'File tidak ditemukan di R2 setelah upload. Periksa kredensial, endpoint, dan pengaturan ACL/visibility bucket.',
            );
        }

        $url = Storage::disk(self::DISK)->url($key).'?v='.time();

        return ['url' => $url, 'key' => $key, 'record' => $record, 'is_new' => ! $existed];
    }

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

    /**
     * @param  array<mixed>  $data
     * @return array<int, string>
     */
    protected function collectStringValues(array $data): array
    {
        $values = [];

        array_walk_recursive($data, static function (mixed $value) use (&$values): void {
            if (is_string($value) && $value !== '') {
                $values[] = $value;
            }
        });

        return $values;
    }

    protected function assertPublicUrlConfigured(): void
    {
        if (blank(config('filesystems.disks.'.self::DISK.'.url'))) {
            throw new RuntimeException('R2_PUBLIC_DOMAIN belum diisi di .env, URL publik gambar tidak bisa dibuat.');
        }
    }
}
