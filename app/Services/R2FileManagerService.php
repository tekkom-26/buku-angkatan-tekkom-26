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
     * List the immediate contents (files + directories) of a directory,
     * reading from the database (source of truth for alias/visibility),
     * reconciled against what currently exists on the disk.
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

        return $query->orderByDesc('is_directory')->orderBy('path')->get();
    }

    /**
     * Create a "directory" — on R2 there's no real directory concept, so we
     * write an empty placeholder object and track it in the DB so it shows
     * up when listing.
     */
    public function createDirectory(string $path, string $visibility = StorageFile::VISIBILITY_PRIVATE, ?string $prefix = null, ?string $createdBy = null): StorageFile
    {
        $path = $this->normalizePath($path);

        if (StorageFile::query()->where('disk', $this->disk)->where('path', $path)->exists()) {
            throw new InvalidArgumentException("Path already exists: {$path}");
        }

        // Placeholder object so the "directory" is visible on the bucket too.
        $this->filesystem()->put($this->keepFileFor($path), '');

        return StorageFile::create([
            'disk' => $this->disk,
            'path' => $path,
            'prefix' => $prefix ?? $this->derivePrefix($path),
            'is_directory' => true,
            'visibility' => $visibility,
            'uploaded_by' => $createdBy,
        ]);
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
            'is_directory' => false,
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

    /**
     * Delete a file (or directory + its descendants) from both the disk and
     * the database.
     */
    public function delete(StorageFile $file): void
    {
        if ($file->is_directory) {
            $descendants = StorageFile::query()
                ->where('disk', $this->disk)
                ->where('path', 'like', $file->path.'/%')
                ->get();

            foreach ($descendants as $descendant) {
                $this->filesystem()->delete($descendant->path);
                $descendant->delete();
            }

            $this->filesystem()->delete($this->keepFileFor($file->path));
        } else {
            $this->filesystem()->delete($file->path);
        }

        $file->delete();
    }

    /**
     * Move/rename a file or directory to a new path.
     */
    public function move(StorageFile $file, string $newPath): StorageFile
    {
        $newPath = $this->normalizePath($newPath);

        if (StorageFile::query()->where('disk', $this->disk)->where('path', $newPath)->exists()) {
            throw new InvalidArgumentException("Destination already exists: {$newPath}");
        }

        if ($file->is_directory) {
            $descendants = StorageFile::query()
                ->where('disk', $this->disk)
                ->where('path', 'like', $file->path.'/%')
                ->get();

            foreach ($descendants as $descendant) {
                $descendantNewPath = $newPath.'/'.Str::after($descendant->path, $file->path.'/');
                $this->filesystem()->move($descendant->path, $descendantNewPath);
                $descendant->update(['path' => $descendantNewPath, 'prefix' => $this->derivePrefix($descendantNewPath)]);
            }

            $this->filesystem()->move($this->keepFileFor($file->path), $this->keepFileFor($newPath));
        } else {
            $this->filesystem()->move($file->path, $newPath);
        }

        $file->update(['path' => $newPath, 'prefix' => $this->derivePrefix($newPath)]);

        return $file;
    }

    /**
     * Generate a short-lived presigned PUT URL so a client can upload
     * directly to R2 without the file passing through the server.
     */
    public function presignedUploadUrl(string $desiredPath, int $expiresInMinutes = 5): array
    {
        $path = $this->normalizePath($desiredPath);

        $result = $this->filesystem()->temporaryUploadUrl($path, now()->addMinutes($expiresInMinutes));

        return [
            'upload_url' => $result['url'],
            'headers' => $result['headers'] ?? [],
            'key' => $path,
            'public_url' => $this->filesystem()->url($path),
        ];
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

    protected function derivePrefix(string $path): ?string
    {
        $segments = explode('/', trim($path, '/'));

        return $segments[0] ?? null;
    }

    protected function keepFileFor(string $directoryPath): string
    {
        return rtrim($directoryPath, '/').'/.keep';
    }
}
