<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\DirectoryPathRequest;
use App\Http\Requests\StoreUploadRequest;
use App\Http\Requests\UpdateStorageFileRequest;
use App\Models\StorageFile;
use App\Services\R2FileManagerService;
use App\Services\R2FileOperationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

/**
 * Public, passcode-protected file manager API (upload / list / edit / delete).
 *
 * Everything here is scoped under a dedicated root prefix
 * (config('angkatan.public_storage.root_prefix'), default "public") and the
 * `visibility = public` flag, so this API can never read, modify, or delete
 * admin-only/private files even if a client supplies an arbitrary path or id.
 */
class PublicStorageController extends Controller
{
    use ApiResponses;

    public function __construct(protected R2FileManagerService $manager) {}

    /**
     * GET /api/storage/public?path=profile-aasokdaok
     *
     * List files/directories directly inside the given path (relative to
     * the public root prefix).
     */
    public function index(Request $request)
    {
        $relative = trim((string) $request->query('path', ''), '/');
        $directory = $this->toAbsolute($relative);

        $files = $this->manager->listDirectory($directory, StorageFile::VISIBILITY_PUBLIC);

        return $this->success([
            'path' => $relative,
            'items' => $files->map(fn (StorageFile|array $item) => $item instanceof StorageFile
                ? $this->present($item)
                : $item)->all(),
        ]);
    }

    /**
     * DELETE /api/storage/public/directories
     * Body: { "path": "profile-aasokdaok/sub-folder" }
     */
    public function destroyDirectory(DirectoryPathRequest $request)
    {
        $relative = trim($request->validated('path'), '/');

        if ($relative === '') {
            return $this->error('INVALID_DIRECTORY', 'A directory path is required.', 422);
        }

        $absolute = $this->toAbsolute($relative);

        try {
            $result = $this->manager->deleteDirectory($absolute, StorageFile::VISIBILITY_PUBLIC);
        } catch (R2FileOperationException $exception) {
            $result = $exception->results;
        }

        return $this->success($result);
    }

    /**
     * POST /api/storage/public/files (multipart/form-data)
     * Fields: file, path (desired directory or filename, relative to public root),
     *         alias (optional, e.g. "profile-aasokdaok-avatar"), overwrite (optional bool)
     */
    public function store(StoreUploadRequest $request)
    {
        $relativePath = $request->validated('path');
        $absolutePath = $this->toAbsolute($relativePath);

        try {
            $file = $this->manager->upload(
                file: $request->file('file'),
                desiredPath: $absolutePath,
                visibility: StorageFile::VISIBILITY_PUBLIC,
                alias: $request->validated('alias'),
                uploadedBy: 'public',
                overwrite: (bool) $request->boolean('overwrite'),
            );
        } catch (InvalidArgumentException $exception) {
            return $this->error('UPLOAD_CONFLICT', $exception->getMessage(), 422);
        }

        return $this->success($this->present($file), 201);
    }

    /**
     * PATCH /api/storage/public/files/{storageFile}
     * Body: { "path"?: "new/relative/path.jpg", "alias"?: "new-alias" }
     */
    public function update(UpdateStorageFileRequest $request, StorageFile $storageFile)
    {
        $this->abortIfNotPublic($storageFile);

        if ($request->has('path')) {
            $newAbsolute = $this->toAbsolute($request->validated('path'));

            try {
                $storageFile = $this->manager->move($storageFile, $newAbsolute);
            } catch (InvalidArgumentException $exception) {
                return $this->error('MOVE_CONFLICT', $exception->getMessage(), 422);
            }
        }

        if ($request->has('alias')) {
            $alias = $request->validated('alias');

            if ($alias && StorageFile::query()->where('alias', $alias)->whereKeyNot($storageFile->id)->exists()) {
                throw ValidationException::withMessages(['alias' => 'Alias already in use.']);
            }

            $storageFile->update(['alias' => $alias]);
        }

        return $this->success($this->present($storageFile->fresh()));
    }

    /**
     * DELETE /api/storage/public/files/{storageFile}
     */
    public function destroy(StorageFile $storageFile)
    {
        $this->abortIfNotPublic($storageFile);

        $this->manager->delete($storageFile);

        return $this->success(['message' => 'Deleted']);
    }

    /**
     * Prefix every path with the public root so this API can never escape
     * into the private/admin area of the bucket.
     */
    protected function toAbsolute(string $relative): string
    {
        $root = trim((string) config('angkatan.public_storage.root_prefix', 'public'), '/');
        $relative = trim($relative, '/');

        return $relative === '' ? $root : $root.'/'.$relative;
    }

    protected function abortIfNotPublic(StorageFile $storageFile): void
    {
        abort_unless($storageFile->isPublic(), 404);
    }

    /**
     * @return array<string, mixed>
     */
    protected function present(StorageFile $file): array
    {
        $root = trim((string) config('angkatan.public_storage.root_prefix', 'public'), '/');

        return [
            'id' => $file->id,
            'path' => $file->path,
            'relative_path' => ltrim(str_replace($root, '', $file->path), '/'),
            'alias' => $file->alias,
            'prefix' => $file->prefix,
            'is_directory' => false,
            'mime_type' => $file->mime_type,
            'size' => $file->size,
            'human_size' => $file->humanSize(),
            'url' => $file->url(),
            'created_at' => $file->created_at?->toIso8601String(),
            'updated_at' => $file->updated_at?->toIso8601String(),
        ];
    }
}
