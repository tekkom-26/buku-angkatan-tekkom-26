<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Services\StudentDirectoryService;
use Database\Seeders\AuthSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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
        ]);
        Student::factory()->create([
            'nim' => '21120126120002',
            'name' => 'Budi Santoso',
            'nickname' => 'Budi',
            'class_name' => 'A',
        ]);

        // Warm up StudentDirectoryService cache
        app(StudentDirectoryService::class)->allPublicStudents();

        DB::enableQueryLog();
        DB::flushQueryLog();

        $this->call('GET', '/directory', [], ['angkatan_session' => $this->sessionCookie])->assertOk();
        $this->call('GET', '/directory', ['class' => 'A'], ['angkatan_session' => $this->sessionCookie])->assertOk();
        $this->call('GET', '/directory', ['search' => 'Adwa'], ['angkatan_session' => $this->sessionCookie])->assertOk();
        $this->call('GET', '/students/21120126120001', [], ['angkatan_session' => $this->sessionCookie])
            ->assertOk()
            ->assertSee('Selanjutnya:')
            ->assertSee('Budi');

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

        // Simulate fresh request instance reading from file cache
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
            ->assertDontSee('Dika Updated');
    }
}
