<?php

namespace Tests\Feature;

use App\Models\Student;
use Database\Seeders\AuthSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class StudentDirectoryScreenTest extends TestCase
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

    public function test_directory_page_renders_filters_search_and_student_cards(): void
    {
        Student::factory()->create([
            'nim' => '21120126120001',
            'name' => 'Adwa Rahmant',
            'nickname' => 'Adwa',
            'class_name' => 'A',
            'origin_city' => 'Semarang',
            'hobbies' => 'Embedded, Jaringan, Linux',
            'formal_photo_url' => '',
        ]);

        $response = $this->call('GET', '/directory', [], [
            'angkatan_session' => $this->sessionCookie,
        ]);

        $response->assertOk();
        $response->assertSee('// 03_DIRECTORY');
        $response->assertSee('Direktori Mahasiswa Angkatan');
        $response->assertSee('1 MAHASISWA TERDATA');
        $response->assertSee('[MODE: UNLOCKED]');
        $response->assertSee('All');
        $response->assertSee('Kelas A');
        $response->assertSee('Kelas B');
        $response->assertSee('Kelas C');
        $response->assertSee('Kelas D');
        $response->assertSee('(1)');
        $response->assertSee('#TK26-001');
        $response->assertSee('alt="Foto Adwa"', false);
        $response->assertSee('Adwa');
        $response->assertSee('Adwa Rahmant');
        $response->assertSee('Lihat Profil');
        $response->assertSee('/assets/Placeholder.png', false);

        $this->assertDoesNotMatchRegularExpression('/(?:bg|text|border|ring)-\[#[0-9a-fA-F]+\]/', $response->getContent());
    }

    public function test_directory_search_returns_matching_students_from_the_server(): void
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
            'class_name' => 'B',
        ]);

        $response = $this->call('GET', '/directory', ['search' => 'Budi'], [
            'angkatan_session' => $this->sessionCookie,
        ]);

        $response->assertOk();
        $response->assertSee('Budi Santoso');
        $this->assertSame(1, $response->viewData('students')->total());
    }

    public function test_directory_search_matches_existing_student_by_keyword_case_insensitively(): void
    {
        Student::factory()->create([
            'name' => 'Adwa Fattur Rahman',
            'nickname' => 'Adwa',
            'class_name' => 'C',
        ]);

        $response = $this->call('GET', '/directory', ['search' => 'adwa'], [
            'angkatan_session' => $this->sessionCookie,
        ]);

        $response->assertOk();
        $response->assertSee('Adwa Fattur Rahman');
        $this->assertSame(1, $response->viewData('students')->total());
    }

    public function test_directory_class_filter_returns_students_from_selected_class(): void
    {
        Student::factory()->create([
            'name' => 'Adwa Kelas A',
            'class_name' => 'A',
        ]);
        Student::factory()->create([
            'name' => 'Budi Kelas B',
            'class_name' => 'B',
        ]);

        $response = $this->call('GET', '/directory', ['class' => 'B'], [
            'angkatan_session' => $this->sessionCookie,
        ]);

        $response->assertOk();
        $response->assertSee('Budi Kelas B');
        $response->assertDontSee('Adwa Kelas A');
        $this->assertSame(1, $response->viewData('students')->total());
    }

    public function test_directory_page_renders_empty_state_when_no_students_match(): void
    {
        $response = $this->call('GET', '/directory', ['search' => 'tidak-ada-mahasiswa-ini'], [
            'angkatan_session' => $this->sessionCookie,
        ]);

        $response->assertOk();
        $response->assertSee('[X] [ICON: USER_NOT_FOUND]');
        $response->assertSee('Tidak ada hasil ditemukan');
        $response->assertSee('Reset Semua Filter');
    }

    public function test_student_card_component_supports_guest_locked_preview_state(): void
    {
        $html = Blade::render('<x-student-card :student="$student" :index="2" :locked="true" />', [
            'student' => [
                'nim' => '21120126120002',
                'name' => 'Budi Santoso',
                'nickname' => 'Budi',
                'class_name' => 'B',
                'origin_city' => 'Jakarta',
            ],
        ]);

        $this->assertStringContainsString('#TK26-002', $html);
        $this->assertStringContainsString('[X] [AVATAR: 1:1 — LOCKED]', $html);
        $this->assertStringContainsString('[PREVIEW: LOCKED]', $html);
        $this->assertStringContainsString('[Terkunci // Butuh Passcode]', $html);
        $this->assertDoesNotMatchRegularExpression('/(?:bg|text|border|ring)-\[#[0-9a-fA-F]+\]/', $html);
    }
}
