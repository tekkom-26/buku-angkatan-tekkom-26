<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Services\StudentDirectoryService;
use App\Services\StudentPhotoService;
use Database\Seeders\AuthSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class ZeroDbStudentDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected string $sessionCookie;

    protected function setUp(): void
    {
        parent::setUp();

        config(['angkatan.passcode' => 'secret-pass']);
        $this->seed(AuthSeeder::class);

        $cookie = $this->postJson('/api/auth/passcode', ['passcode' => 'secret-pass'])
            ->getCookie('angkatan_session', false);

        $this->sessionCookie = $cookie->getValue();
    }

    public function test_directory_and_profile_execute_zero_student_queries_when_cache_is_warm(): void
    {
        Student::factory()->create([
            'nim' => '21120126120001',
            'name' => 'Adwa Rahmant',
            'nickname' => 'Adwa',
            'class_name' => 'A',
            'origin_city' => 'Semarang',
        ]);
        Student::factory()->create([
            'nim' => '21120126120002',
            'name' => 'Budi Santoso',
            'nickname' => 'Budi',
            'class_name' => 'A',
            'origin_city' => 'Surabaya',
        ]);

        // Warm up StudentDirectoryService cache, then clear scoped instances to simulate fresh HTTP requests
        app(StudentDirectoryService::class)->allPublicStudents();
        $this->app->forgetScopedInstances();

        DB::enableQueryLog();
        DB::flushQueryLog();

        $this->call('GET', '/directory', [], ['angkatan_session' => $this->sessionCookie])->assertOk();
        $this->call('GET', '/directory', ['class' => 'A'], ['angkatan_session' => $this->sessionCookie])->assertOk();
        $this->call('GET', '/directory', ['search' => 'Adwa'], ['angkatan_session' => $this->sessionCookie])->assertOk();
        $this->call('GET', '/directory', ['search' => 'surabaya'], ['angkatan_session' => $this->sessionCookie])
            ->assertOk()
            ->assertSee('Budi Santoso');
        $this->call('GET', '/directory', ['page' => 2], ['angkatan_session' => $this->sessionCookie])->assertOk();
        $this->call('GET', '/students/21120126120001', [], ['angkatan_session' => $this->sessionCookie])
            ->assertOk()
            ->assertSee('Selanjutnya:')
            ->assertSee('Budi');
        $this->call('GET', '/students/21120126129999', [], ['angkatan_session' => $this->sessionCookie])
            ->assertNotFound();

        $studentQueries = collect(DB::getQueryLog())
            ->filter(fn (array $log): bool => str_contains(strtolower($log['query']), 'students'))
            ->reject(fn (array $log): bool => str_contains(strtolower($log['query']), 'student_sessions'));

        $this->assertCount(0, $studentQueries, 'Expected 0 queries to students table when cache is warm.');
    }

    public function test_student_directory_service_is_compatible_with_file_cache_driver(): void
    {
        Config::set('cache.default', 'file');

        Student::factory()->create([
            'nim' => '21120126120010',
            'name' => 'Citra Dewi',
            'nickname' => 'Citra',
            'class_name' => 'C',
        ]);

        $service = app(StudentDirectoryService::class);
        $service->clearCache();

        $first = $service->allPublicStudents();
        $this->assertCount(1, $first);
        $this->assertSame('Citra Dewi', $first->first()['name']);

        // Simulate fresh request instance reading from file cache with serializable_classes => false
        $freshService = new StudentDirectoryService;
        $second = $freshService->allPublicStudents();
        $this->assertCount(1, $second);
        $this->assertSame('Citra Dewi', $second->first()['name']);

        $service->clearCache();
    }

    public function test_observer_invalidates_directory_cache_on_student_create_update_and_delete(): void
    {
        $student = Student::factory()->create([
            'nim' => '21120126120020',
            'name' => 'Dika Pratama',
            'nickname' => 'Dika',
            'class_name' => 'D',
        ]);

        // Warm cache
        $this->call('GET', '/directory', [], ['angkatan_session' => $this->sessionCookie])
            ->assertOk()
            ->assertSee('Dika Pratama');

        // Create another student while cache is already warm
        Student::factory()->create([
            'nim' => '21120126120021',
            'name' => 'Eko Saputra',
            'nickname' => 'Eko',
            'class_name' => 'D',
        ]);

        $this->call('GET', '/directory', [], ['angkatan_session' => $this->sessionCookie])
            ->assertOk()
            ->assertSee('Eko Saputra');

        // Update student
        $student->update(['name' => 'Dika Updated']);

        $this->call('GET', '/directory', [], ['angkatan_session' => $this->sessionCookie])
            ->assertOk()
            ->assertSee('Dika Updated')
            ->assertDontSee('Dika Pratama');

        // Delete student
        $student->delete();

        $this->call('GET', '/directory', [], ['angkatan_session' => $this->sessionCookie])
            ->assertOk()
            ->assertDontSee('Dika Updated')
            ->assertSee('Eko Saputra');
    }

    public function test_observer_fires_only_once_per_student_lifecycle_event(): void
    {
        $directorySpy = Mockery::mock(StudentDirectoryService::class)->makePartial();
        $directorySpy->shouldReceive('clearCache')->twice();
        $this->app->instance(StudentDirectoryService::class, $directorySpy);

        $photoSpy = Mockery::mock(StudentPhotoService::class);
        $photoSpy->shouldReceive('deletePhotos')->once();
        $this->app->instance(StudentPhotoService::class, $photoSpy);

        $student = Student::factory()->create([
            'nim' => '21120126120030',
            'name' => 'Fajar Nugroho',
            'class_name' => 'B',
        ]);

        $student->delete();
    }

    public function test_profile_payload_computes_accurate_in_class_navigation_and_position(): void
    {
        Student::factory()->create(['nim' => '21120126120003', 'name' => 'Candra', 'nickname' => 'Can', 'class_name' => 'A']);
        Student::factory()->create(['nim' => '21120126120001', 'name' => 'Andi', 'nickname' => 'An', 'class_name' => 'A']);
        Student::factory()->create(['nim' => '21120126120002', 'name' => 'Budi', 'nickname' => 'Bu', 'class_name' => 'A']);
        Student::factory()->create(['nim' => '21120126120004', 'name' => 'Deni', 'nickname' => 'De', 'class_name' => 'B']);

        $service = app(StudentDirectoryService::class);

        $first = $service->getStudentProfilePayload('21120126120001');
        $this->assertNotNull($first);
        $this->assertNull($first['prevStudent']);
        $this->assertSame('21120126120002', $first['nextStudent']['nim']);
        $this->assertSame(1, $first['currentPosition']);
        $this->assertSame(3, $first['totalInClass']);

        $middle = $service->getStudentProfilePayload('21120126120002');
        $this->assertNotNull($middle);
        $this->assertSame('21120126120001', $middle['prevStudent']['nim']);
        $this->assertSame('21120126120003', $middle['nextStudent']['nim']);
        $this->assertSame(2, $middle['currentPosition']);
        $this->assertSame(3, $middle['totalInClass']);

        $last = $service->getStudentProfilePayload('21120126120003');
        $this->assertNotNull($last);
        $this->assertSame('21120126120002', $last['prevStudent']['nim']);
        $this->assertNull($last['nextStudent']);
        $this->assertSame(3, $last['currentPosition']);
        $this->assertSame(3, $last['totalInClass']);
    }
}
