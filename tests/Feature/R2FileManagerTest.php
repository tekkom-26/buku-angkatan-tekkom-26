<?php

namespace Tests\Feature;

use App\Models\StorageFile;
use App\Services\R2FileManagerService;
use App\Services\R2FileOperationException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class R2FileManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('r2');
    }

    public function test_list_directory_returns_direct_files_and_unique_virtual_folders(): void
    {
        $directFile = StorageFile::factory()->create([
            'path' => 'public/team/direct.jpg',
            'prefix' => 'team',
        ]);
        StorageFile::factory()->create(['path' => 'public/team/nested/one.jpg', 'prefix' => 'team']);
        StorageFile::factory()->create(['path' => 'public/team/nested/two.jpg', 'prefix' => 'team']);
        StorageFile::factory()->create(['path' => 'public/team-other/ignored.jpg', 'prefix' => 'team-other']);

        $items = app(R2FileManagerService::class)->listDirectory('public/team', StorageFile::VISIBILITY_PUBLIC);

        $this->assertCount(2, $items);
        $this->assertSame($directFile->id, $items->first(fn ($item) => $item instanceof StorageFile)->id);
        $this->assertSame([
            'path' => 'public/team/nested',
            'is_directory' => true,
        ], $items->first(fn ($item) => is_array($item)));
    }

    public function test_move_directory_moves_all_descendants_and_updates_paths(): void
    {
        $firstFile = StorageFile::factory()->create(['path' => 'public/old/a.jpg', 'prefix' => 'old']);
        $secondFile = StorageFile::factory()->create(['path' => 'public/old/nested/b.jpg', 'prefix' => 'old']);
        Storage::disk('r2')->put($firstFile->path, 'a');
        Storage::disk('r2')->put($secondFile->path, 'b');

        $result = app(R2FileManagerService::class)->moveDirectory('public/old', 'public/new');

        $this->assertSame([
            ['from' => 'public/old/a.jpg', 'to' => 'public/new/a.jpg'],
            ['from' => 'public/old/nested/b.jpg', 'to' => 'public/new/nested/b.jpg'],
        ], $result['moved']);
        $this->assertSame([], $result['failed']);
        $this->assertDatabaseHas('storage_files', ['id' => $firstFile->id, 'path' => 'public/new/a.jpg']);
        $this->assertDatabaseHas('storage_files', ['id' => $secondFile->id, 'path' => 'public/new/nested/b.jpg']);
        Storage::disk('r2')->assertExists('public/new/a.jpg');
        Storage::disk('r2')->assertExists('public/new/nested/b.jpg');
    }

    public function test_delete_directory_commits_successes_and_collects_item_failures(): void
    {
        $deletedFile = StorageFile::factory()->create(['path' => 'public/team/a.jpg']);
        $failedFile = StorageFile::factory()->create(['path' => 'public/team/b.jpg']);
        $otherVisibilityFile = StorageFile::factory()->private()->create(['path' => 'public/team/private.jpg']);
        $similarPrefixFile = StorageFile::factory()->create(['path' => 'public/team-other/c.jpg']);

        $filesystem = \Mockery::mock(Filesystem::class);
        $filesystem->shouldReceive('delete')->with('public/team/a.jpg')->once()->andReturn(true);
        $filesystem->shouldReceive('delete')->with('public/team/b.jpg')->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('r2')->twice()->andReturn($filesystem);

        try {
            app(R2FileManagerService::class)->deleteDirectory('public/team', StorageFile::VISIBILITY_PUBLIC);
            $this->fail('Expected a partial-operation exception.');
        } catch (R2FileOperationException $exception) {
            $this->assertSame(['public/team/a.jpg'], $exception->results['deleted']);
            $this->assertSame('public/team/b.jpg', $exception->results['failed'][0]['path']);
            $this->assertNotEmpty($exception->results['failed'][0]['error']);
        }

        $this->assertDatabaseMissing('storage_files', ['id' => $deletedFile->id]);
        $this->assertDatabaseHas('storage_files', ['id' => $failedFile->id]);
        $this->assertDatabaseHas('storage_files', ['id' => $otherVisibilityFile->id]);
        $this->assertDatabaseHas('storage_files', ['id' => $similarPrefixFile->id]);
    }

    public function test_move_directory_commits_successes_and_collects_item_failures(): void
    {
        $movedFile = StorageFile::factory()->create(['path' => 'public/team/a.jpg']);
        $failedFile = StorageFile::factory()->create(['path' => 'public/team/b.jpg']);

        $filesystem = \Mockery::mock(Filesystem::class);
        $filesystem->shouldReceive('move')->with('public/team/a.jpg', 'public/new/a.jpg')->once()->andReturn(true);
        $filesystem->shouldReceive('move')->with('public/team/b.jpg', 'public/new/b.jpg')->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('r2')->twice()->andReturn($filesystem);

        try {
            app(R2FileManagerService::class)->moveDirectory('public/team', 'public/new');
            $this->fail('Expected a partial-operation exception.');
        } catch (R2FileOperationException $exception) {
            $this->assertSame([
                ['from' => 'public/team/a.jpg', 'to' => 'public/new/a.jpg'],
            ], $exception->results['moved']);
            $this->assertSame('public/team/b.jpg', $exception->results['failed'][0]['path']);
        }

        $this->assertDatabaseHas('storage_files', ['id' => $movedFile->id, 'path' => 'public/new/a.jpg']);
        $this->assertDatabaseHas('storage_files', ['id' => $failedFile->id, 'path' => 'public/team/b.jpg']);
    }

    public function test_public_directory_delete_is_root_scoped_and_returns_partial_result_shape(): void
    {
        config(['angkatan.passcode' => 'test-passcode']);

        $file = StorageFile::factory()->create(['path' => 'public/team/a.jpg']);
        $failedFile = StorageFile::factory()->create(['path' => 'public/team/b.jpg']);
        $outsideFile = StorageFile::factory()->create(['path' => 'private/team/b.jpg']);

        $filesystem = \Mockery::mock(Filesystem::class);
        $filesystem->shouldReceive('delete')->with('public/team/a.jpg')->once()->andReturn(true);
        $filesystem->shouldReceive('delete')->with('public/team/b.jpg')->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('r2')->twice()->andReturn($filesystem);

        $response = $this->withHeader('X-Angkatan-Passcode', 'test-passcode')
            ->deleteJson('/api/storage/public/directories', ['path' => 'team']);

        $response->assertOk()
            ->assertJsonPath('data.deleted', ['public/team/a.jpg'])
            ->assertJsonPath('data.failed.0.path', 'public/team/b.jpg')
            ->assertJsonPath('data.failed.0.error', 'Could not delete R2 object: public/team/b.jpg');
        $this->assertDatabaseMissing('storage_files', ['id' => $file->id]);
        $this->assertDatabaseHas('storage_files', ['id' => $failedFile->id]);
        $this->assertDatabaseHas('storage_files', ['id' => $outsideFile->id]);
    }

    public function test_public_directory_delete_rejects_the_root_path(): void
    {
        config(['angkatan.passcode' => 'test-passcode']);

        $this->withHeader('X-Angkatan-Passcode', 'test-passcode')
            ->deleteJson('/api/storage/public/directories', ['path' => '/'])
            ->assertUnprocessable()
            ->assertJsonPath('error.code', 'INVALID_DIRECTORY');
    }
}
