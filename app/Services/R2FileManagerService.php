<?php

namespace App\Services;

use App\Models\StorageFile;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 * Central service for managing files on the Cloudflare R2 bucket (via the
 * "r2" Flysystem disk) while keeping a mirrored record in the `storage_files`
 * table (path, alias, prefix, visibility, metadata).
 *
 * Both the private (Filament admin) file manager and the public API share
 * this service so object storage and the database never drift apart.
 */
class R2FileManagerService
{
    public function __construct(
        protected string $disk = 'r2',
    ) {}

    protected function filesystem(): Filesystem
    {
        return Storage::disk($this->disk);
    }

    /**
     * List direct files and virtual child directories using one database query.
     * Virtual directories are derived from descendant file paths in memory.
     *
     * @return Collection<int, StorageFile|array{path: string, is_directory: true}>
     */
    public function listDirectory(?string $directory = null, ?string $visibility = null): Collection
    {
        $directory = $this->normalizeDirectory($directory);

        $query = StorageFile::query()
            ->where('disk', $this->disk)
            ->inDirectory($directory);

        if ($visibility !== null) {
            $query->where('visibility', $visibility);
        }

        $files = $query->orderBy('path')->get();
        $items = [];
        $basePath = $directory === '' ? '' : $directory.'/';

        foreach ($files as $file) {
            $relativePath = Str::after($file->path, $basePath);
            $separatorPosition = strpos($relativePath, '/');

            if ($separatorPosition === false) {
                $items['file:'.$file->path] = $file;

                continue;
            }

            $folderPath = $basePath.substr($relativePath, 0, $separatorPosition);
            $items['directory:'.$folderPath] = [
                'path' => $folderPath,
                'is_directory' => true,
            ];
        }

        return collect($items)
            ->sortBy(fn (StorageFile|array $item) => $item instanceof StorageFile ? $item->path : $item['path'])
            ->values();
    }

    /**
     * Upload a file to a desired path on R2 and record it in the DB.
     *
     * @param  string  $desiredPath  Directory (or full key) where the file should live, e.g. "avatars" or "avatars/21000123.jpg".
     */
    public function upload(
        UploadedFile $file,
        string $desiredPath,
        string $visibility = StorageFile::VISIBILITY_PRIVATE,
        ?string $alias = null,
        ?string $prefix = null,
        ?string $uploadedBy = null,
        bool $overwrite = false,
    ): StorageFile {
        $path = $this->resolveUploadPath($desiredPath, $file);

        $existing = StorageFile::query()->where('disk', $this->disk)->where('path', $path)->first();

        if ($existing && ! $overwrite) {
            throw new InvalidArgumentException("A file already exists at path: {$path}");
        }

        if ($alias && StorageFile::query()
            ->where('alias', $alias)
            ->when($existing, fn ($q) => $q->whereKeyNot($existing->id))
            ->exists()
        ) {
            throw new InvalidArgumentException("Alias already in use: {$alias}");
        }

        $this->filesystem()->put($path, file_get_contents($file->getRealPath()), [
            'visibility' => $visibility === StorageFile::VISIBILITY_PUBLIC ? 'public' : 'private',
        ]);

        $attributes = [
            'disk' => $this->disk,
            'path' => $path,
            'prefix' => $prefix ?? $this->derivePrefix($path),
            'alias' => $alias,
            'visibility' => $visibility,
            'uploaded_by' => $uploadedBy,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ];

        if ($existing) {
            $existing->fill($attributes);
            $existing->save();

            return $existing;
        }

        return StorageFile::create($attributes);
    }

    /** Delete a file from R2 and then remove its database record. */
    public function delete(StorageFile $file): void
    {
        if (! $this->filesystem()->delete($file->path)) {
            throw new RuntimeException("Could not delete R2 object: {$file->path}");
        }

        $file->delete();
    }

    /**
     * Move or rename a single file to a new path.
     */
    public function move(StorageFile $file, string $newPath): StorageFile
    {
        $newPath = $this->normalizePath($newPath);

        if (StorageFile::query()->where('disk', $this->disk)->where('path', $newPath)->exists()) {
            throw new InvalidArgumentException("Destination already exists: {$newPath}");
        }

        if (! $this->filesystem()->move($file->path, $newPath)) {
            throw new RuntimeException("Could not move R2 object: {$file->path}");
        }

        $file->update(['path' => $newPath, 'prefix' => $this->derivePrefix($newPath)]);

        return $file;
    }

    /**
     * Delete all files below a virtual directory, committing each successful item.
     *
     * @return array{deleted: array<int, string>, failed: array<int, array{path: string, error: string}>}
     */
    public function deleteDirectory(string $path, ?string $visibility = null): array
    {
        $files = $this->directoryFiles($path, $visibility);
        $deleted = [];
        $failed = [];

        foreach ($files as $file) {
            try {
                if (! $this->filesystem()->delete($file->path)) {
                    throw new RuntimeException("Could not delete R2 object: {$file->path}");
                }

                $file->delete();
                $deleted[] = $file->path;
            } catch (Throwable $exception) {
                $failed[] = ['path' => $file->path, 'error' => $exception->getMessage()];
            }
        }

        $results = compact('deleted', 'failed');

        if ($failed !== []) {
            throw new R2FileOperationException($results);
        }

        return $results;
    }

    /**
     * Move every file below a virtual directory, committing each successful item.
     *
     * @return array{moved: array<int, array{from: string, to: string}>, failed: array<int, array{path: string, error: string}>}
     */
    public function moveDirectory(string $oldPrefix, string $newPrefix, ?string $visibility = null): array
    {
        $oldPrefix = $this->normalizePath($oldPrefix);
        $newPrefix = $this->normalizePath($newPrefix);

        if ($oldPrefix === $newPrefix || str_starts_with($newPrefix, $oldPrefix.'/')) {
            throw new InvalidArgumentException('The destination must be different and outside the source directory.');
        }

        $files = $this->directoryFiles($oldPrefix, $visibility);
        $moved = [];
        $failed = [];

        foreach ($files as $file) {
            $oldPath = $file->path;
            $newPath = $newPrefix.'/'.Str::after($oldPath, $oldPrefix.'/');

            try {
                if (StorageFile::query()->where('disk', $this->disk)->where('path', $newPath)->exists()) {
                    throw new RuntimeException("Destination already exists: {$newPath}");
                }

                if (! $this->filesystem()->move($oldPath, $newPath)) {
                    throw new RuntimeException("Could not move R2 object to: {$newPath}");
                }

                $file->update(['path' => $newPath, 'prefix' => $this->derivePrefix($newPath)]);
                $moved[] = ['from' => $oldPath, 'to' => $newPath];
            } catch (Throwable $exception) {
                $failed[] = ['path' => $oldPath, 'error' => $exception->getMessage()];
            }
        }

        $results = compact('moved', 'failed');

        if ($failed !== []) {
            throw new R2FileOperationException($results);
        }

        return $results;
    }

    /**
     * Resolve a presigned/public URL for downloading a file.
     */
    public function urlFor(StorageFile $file, int $temporaryUrlMinutes = 5): ?string
    {
        return $file->url($temporaryUrlMinutes);
    }

    protected function resolveUploadPath(string $desiredPath, UploadedFile $file): string
    {
        $desiredPath = trim($desiredPath, '/');

        // If desiredPath already looks like a full filename (has an extension
        // matching the uploaded file, or simply ends in a dot-extension),
        // treat it as the exact target path. Otherwise treat it as a
        // directory and append the original filename.
        $looksLikeFile = (bool) preg_match('/\.[A-Za-z0-9]{1,10}$/', $desiredPath);

        if ($looksLikeFile) {
            return $this->normalizePath($desiredPath);
        }

        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME))
            .'-'.Str::random(8)
            .'.'.$file->getClientOriginalExtension();

        return $this->normalizePath($desiredPath.'/'.$filename);
    }

    protected function normalizeDirectory(?string $directory): string
    {
        return trim((string) $directory, '/');
    }

    protected function normalizePath(string $path): string
    {
        $path = trim($path, '/');

        if ($path === '') {
            throw new InvalidArgumentException('Path cannot be empty.');
        }

        return $path;
    }

    public function derivePrefix(string $path): ?string
    {
        $segments = explode('/', trim($path, '/'));

        $publicRoot = trim((string) config('angkatan.public_storage.root_prefix', 'public'), '/');

        if (($segments[0] ?? null) === $publicRoot) {
            array_shift($segments);
        }

        return $segments[0] ?? null;
    }

    /** @return Collection<int, StorageFile> */
    protected function directoryFiles(string $path, ?string $visibility): Collection
    {
        $path = $this->normalizePath($path);

        return StorageFile::query()
            ->where('disk', $this->disk)
            ->where('path', 'like', $path.'/%')
            ->when($visibility !== null, fn ($query) => $query->where('visibility', $visibility))
            ->orderBy('path')
            ->get();
    }
}
