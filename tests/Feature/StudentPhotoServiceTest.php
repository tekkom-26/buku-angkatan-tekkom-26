<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\StorageFile;
use App\Models\Student;
use App\Services\R2FileManagerService;
use App\Services\StudentPhotoService;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class StudentPhotoServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['filesystems.disks.r2.url' => 'https://cdn.subekti.web.id']);
        Storage::fake('local');
        Storage::fake('r2', ['url' => 'https://cdn.subekti.web.id']);
    }

    public function test_create_uploads_formal_and_casual_photos_under_students_nim_directory(): void
    {
        $formalTemp = $this->stageFakeImage('formal.jpeg');
        $casualTemp = $this->stageFakeImage('casual.png');

        $result = app(StudentPhotoService::class)->processFormData([
            'nim' => '21120126140197',
            'formal_photo_upload' => $formalTemp,
            'casual_photo_upload' => $casualTemp,
        ]);

        $this->assertArrayNotHasKey('formal_photo_upload', $result);
        $this->assertArrayNotHasKey('casual_photo_upload', $result);

        $this->assertMatchesRegularExpression(
            '#^https://cdn\.subekti\.web\.id/students/21120126140197/formal\.jpeg\?v=\d+$#',
            $result['formal_photo_url']
        );
        $this->assertMatchesRegularExpression(
            '#^https://cdn\.subekti\.web\.id/students/21120126140197/casual\.png\?v=\d+$#',
            $result['casual_photo_url']
        );

        Storage::disk('r2')->assertExists('students/21120126140197/formal.jpeg');
        Storage::disk('r2')->assertExists('students/21120126140197/casual.png');
        Storage::disk('local')->assertMissing($formalTemp);
        Storage::disk('local')->assertMissing($casualTemp);

        $this->assertDatabaseHas('storage_files', [
            'disk' => 'r2',
            'path' => 'students/21120126140197/formal.jpeg',
            'prefix' => 'students',
            'visibility' => StorageFile::VISIBILITY_PUBLIC,
        ]);
        $this->assertDatabaseHas('storage_files', [
            'disk' => 'r2',
            'path' => 'students/21120126140197/casual.png',
            'prefix' => 'students',
            'visibility' => StorageFile::VISIBILITY_PUBLIC,
        ]);
    }

    public function test_update_removes_existing_seeded_file_and_reuploads_at_same_students_path(): void
    {
        Storage::disk('r2')->put('students/21120126140197/casual.jpeg', 'old-seeded-image-bytes');

        $student = Student::factory()->create([
            'nim' => '21120126140197',
            'formal_photo_url' => 'https://cdn.subekti.web.id/students/21120126140197/formal.jpeg',
            'casual_photo_url' => 'https://cdn.subekti.web.id/students/21120126140197/casual.jpeg',
        ]);

        $newCasualTemp = $this->stageFakeImage('new-casual.jpeg');

        $result = app(StudentPhotoService::class)->processFormData([
            'nim' => '21120126140197',
            'casual_photo_upload' => $newCasualTemp,
        ], $student);

        $this->assertMatchesRegularExpression(
            '#^https://cdn\.subekti\.web\.id/students/21120126140197/casual\.jpeg\?v=\d+$#',
            $result['casual_photo_url']
        );
        $this->assertArrayNotHasKey('formal_photo_url', $result);

        Storage::disk('r2')->assertExists('students/21120126140197/casual.jpeg');
        $this->assertNotSame(
            'old-seeded-image-bytes',
            Storage::disk('r2')->get('students/21120126140197/casual.jpeg')
        );
        Storage::disk('local')->assertMissing($newCasualTemp);

        $this->assertDatabaseHas('storage_files', [
            'disk' => 'r2',
            'path' => 'students/21120126140197/casual.jpeg',
            'prefix' => 'students',
            'visibility' => StorageFile::VISIBILITY_PUBLIC,
        ]);
    }

    public function test_update_removes_legacy_profile_prefix_or_different_extension_and_uploads_under_students(): void
    {
        Storage::disk('r2')->put('profile-21120126140134/formal.jpg', 'legacy-formal-bytes');
        $legacyRecord = StorageFile::factory()->public()->create([
            'disk' => 'r2',
            'path' => 'profile-21120126140134/formal.jpg',
            'prefix' => 'profile-21120126140134',
        ]);

        Storage::disk('r2')->put('students/21120126140134/casual.jpeg', 'old-casual-bytes');

        $student = Student::factory()->create([
            'nim' => '21120126140134',
            'formal_photo_url' => 'https://cdn.subekti.web.id/profile-21120126140134/formal.jpg?v=1795514880',
            'casual_photo_url' => 'https://cdn.subekti.web.id/students/21120126140134/casual.jpeg',
        ]);

        $newFormalTemp = $this->stageFakeImage('formal-updated.webp');
        $newCasualTemp = $this->stageFakeImage('casual-updated.jpg');

        $result = app(StudentPhotoService::class)->processFormData([
            'nim' => '21120126140134',
            'formal_photo_upload' => $newFormalTemp,
            'casual_photo_upload' => $newCasualTemp,
        ], $student);

        Storage::disk('r2')->assertMissing('profile-21120126140134/formal.jpg');
        Storage::disk('r2')->assertMissing('students/21120126140134/casual.jpeg');
        $this->assertDatabaseMissing('storage_files', ['id' => $legacyRecord->id]);

        Storage::disk('r2')->assertExists('students/21120126140134/formal.webp');
        Storage::disk('r2')->assertExists('students/21120126140134/casual.jpg');

        $this->assertMatchesRegularExpression(
            '#^https://cdn\.subekti\.web\.id/students/21120126140134/formal\.webp\?v=\d+$#',
            $result['formal_photo_url']
        );
        $this->assertMatchesRegularExpression(
            '#^https://cdn\.subekti\.web\.id/students/21120126140134/casual\.jpg\?v=\d+$#',
            $result['casual_photo_url']
        );
    }

    public function test_update_does_not_delete_existing_photo_when_staged_file_is_missing(): void
    {
        Storage::disk('r2')->put('students/21120126140197/casual.jpeg', 'keep-me');

        $student = Student::factory()->create([
            'nim' => '21120126140197',
            'formal_photo_url' => 'https://cdn.subekti.web.id/students/21120126140197/formal.jpeg',
            'casual_photo_url' => 'https://cdn.subekti.web.id/students/21120126140197/casual.jpeg',
        ]);

        $this->expectException(RuntimeException::class);

        try {
            app(StudentPhotoService::class)->processFormData([
                'nim' => '21120126140197',
                'casual_photo_upload' => 'tmp-uploads/does-not-exist.jpeg',
            ], $student);
        } finally {
            Storage::disk('r2')->assertExists('students/21120126140197/casual.jpeg');
            $this->assertSame('keep-me', Storage::disk('r2')->get('students/21120126140197/casual.jpeg'));
        }
    }

    public function test_deleting_student_deletes_students_directory_photos_and_records(): void
    {
        $formalTemp = $this->stageFakeImage('formal.jpeg');
        $casualTemp = $this->stageFakeImage('casual.jpeg');

        $urls = app(StudentPhotoService::class)->processFormData([
            'nim' => '21120126140199',
            'formal_photo_upload' => $formalTemp,
            'casual_photo_upload' => $casualTemp,
        ]);

        $student = Student::factory()->create([
            'nim' => '21120126140199',
            'formal_photo_url' => $urls['formal_photo_url'],
            'casual_photo_url' => $urls['casual_photo_url'],
        ]);

        $student->delete();

        Storage::disk('r2')->assertMissing('students/21120126140199/formal.jpeg');
        Storage::disk('r2')->assertMissing('students/21120126140199/casual.jpeg');
        $this->assertDatabaseMissing('storage_files', [
            'disk' => 'r2',
            'path' => 'students/21120126140199/formal.jpeg',
        ]);
        $this->assertDatabaseMissing('storage_files', [
            'disk' => 'r2',
            'path' => 'students/21120126140199/casual.jpeg',
        ]);
    }

    public function test_update_picks_latest_existing_staged_file_when_stale_key_is_present(): void
    {
        Storage::disk('r2')->put('students/21120126120021/casual.jpeg', 'old-casual-bytes');

        $student = Student::factory()->create([
            'nim' => '21120126120021',
            'formal_photo_url' => 'https://cdn.subekti.web.id/students/21120126120021/formal.jpeg',
            'casual_photo_url' => 'https://cdn.subekti.web.id/students/21120126120021/casual.jpeg',
        ]);

        $freshCasualTemp = $this->stageFakeImage('fresh-casual.jpg');

        $result = app(StudentPhotoService::class)->processFormData([
            'nim' => '21120126120021',
            'casual_photo_upload' => [
                '33b9270d-f97c-4a87-b045-fb2807b03f1f' => 'tmp-uploads/stale-deleted.jpg',
                'f94edd51-680a-4be1-a76e-6d9e79ed9366' => $freshCasualTemp,
            ],
        ], $student);

        Storage::disk('r2')->assertMissing('students/21120126120021/casual.jpeg');
        Storage::disk('r2')->assertExists('students/21120126120021/casual.jpg');
        Storage::disk('local')->assertMissing($freshCasualTemp);
        $this->assertMatchesRegularExpression(
            '#^https://cdn\.subekti\.web\.id/students/21120126120021/casual\.jpg\?v=\d+$#',
            $result['casual_photo_url']
        );
    }

    public function test_failed_r2_put_throws_runtime_exception_without_creating_orphan_storage_file(): void
    {
        $fakeFile = UploadedFile::fake()->image('casual.jpg', 300, 400);

        $failingDisk = Mockery::mock(Filesystem::class);
        $failingDisk->shouldReceive('put')->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('r2')->andReturn($failingDisk);

        $service = new R2FileManagerService('r2');

        $this->expectException(RuntimeException::class);

        try {
            $service->upload(
                file: $fakeFile,
                desiredPath: 'students/21120126120021/casual.jpg',
                visibility: StorageFile::VISIBILITY_PUBLIC,
                overwrite: true,
            );
        } finally {
            $this->assertDatabaseMissing('storage_files', [
                'disk' => 'r2',
                'path' => 'students/21120126120021/casual.jpg',
            ]);
        }
    }

    protected function stageFakeImage(string $filename): string
    {
        $file = UploadedFile::fake()->image($filename, 300, 400);

        return Storage::disk('local')->putFileAs('tmp-uploads', $file, uniqid('tmp-', true).'-'.$filename);
    }
}
