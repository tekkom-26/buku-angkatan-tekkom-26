<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Database record for a file (or directory marker) stored on a Flysystem disk
 * (Cloudflare R2 by default). Acts as the source of truth for aliases,
 * visibility (public/private) and the scanning "prefix" used to group
 * related uploads (e.g. "profile-aasokdaok/...").
 */
#[Fillable([
    'disk',
    'path',
    'prefix',
    'alias',
    'is_directory',
    'visibility',
    'uploaded_by',
    'original_name',
    'mime_type',
    'size',
    'metadata',
])]
#[Hidden([])]
class StorageFile extends Model
{
    use HasFactory, HasUuids;

    public const VISIBILITY_PUBLIC = 'public';

    public const VISIBILITY_PRIVATE = 'private';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_directory' => 'boolean',
            'size' => 'integer',
            'metadata' => 'array',
        ];
    }

    /**
     * Scope a query to only public files/directories.
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visibility', self::VISIBILITY_PUBLIC);
    }

    /**
     * Scope a query to only private files/directories.
     */
    public function scopePrivate(Builder $query): Builder
    {
        return $query->where('visibility', self::VISIBILITY_PRIVATE);
    }

    /**
     * Scope a query to records under a given prefix (top-level folder used
     * for scanning, e.g. "profile-aasokdaok").
     */
    public function scopeByPrefix(Builder $query, string $prefix): Builder
    {
        return $query->where('prefix', $prefix);
    }

    /**
     * Scope a query to records directly inside a given directory path
     * (non-recursive "ls").
     */
    public function scopeInDirectory(Builder $query, ?string $directory): Builder
    {
        $directory = trim((string) $directory, '/');

        return $query->where('path', 'like', ($directory !== '' ? $directory.'/' : '').'%')
            ->whereRaw(
                "path NOT LIKE ?",
                [($directory !== '' ? $directory.'/' : '').'%/%']
            );
    }

    public function scopeOnlyFiles(Builder $query): Builder
    {
        return $query->where('is_directory', false);
    }

    public function scopeOnlyDirectories(Builder $query): Builder
    {
        return $query->where('is_directory', true);
    }

    public function isPublic(): bool
    {
        return $this->visibility === self::VISIBILITY_PUBLIC;
    }

    /**
     * Resolve a URL to the file. Public files resolve to the disk's public
     * URL (R2 public domain / CDN). Private files resolve to a short-lived
     * temporary (presigned) URL, since the bucket is not meant to be public.
     */
    public function url(int $temporaryUrlMinutes = 5): ?string
    {
        if ($this->is_directory) {
            return null;
        }

        $disk = Storage::disk($this->disk);

        if ($this->isPublic()) {
            return $disk->url($this->path);
        }

        return $disk->temporaryUrl($this->path, now()->addMinutes($temporaryUrlMinutes));
    }

    public function humanSize(): string
    {
        $bytes = (int) $this->size;

        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        return number_format($bytes / (1024 ** $power), 2).' '.$units[$power];
    }
}
