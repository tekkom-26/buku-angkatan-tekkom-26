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
        $response->assertSee('#TK26-001');
        $response->assertSee('Foto Adwa', false);
        $response->assertSee('Adwa');
        $response->assertSee('Adwa Rahmant');
        $response->assertSee('Lihat Profil');
        $response->assertSee('/assets/Placeholder.png', false);

        $this->assertDoesNotMatchRegularExpression('/(?:bg|text|border|ring)-\[#[0-9a-fA-F]+\]/', $response->getContent());
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

    public function test_search_is_case_insensitive_and_covers_students_beyond_first_page(): void
    {
        Student::factory()->count(20)->create(['class_name' => 'B']);
        Student::factory()->create([
            'nim' => '21120126129999',
            'name' => 'Adwa Rahmant',
            'nickname' => 'Adwa',
            'class_name' => 'D',
        ]);

        $response = $this->call('GET', '/directory', ['search' => 'adw'], [
            'angkatan_session' => $this->sessionCookie,
        ]);

        $response->assertOk();
        $response->assertSee('Adwa Rahmant');
        $response->assertSee('21120126129999');
        $response->assertDontSee('Tidak ada hasil ditemukan');
    }

    public function test_class_filter_is_applied_by_server_and_combined_with_search(): void
    {
        Student::factory()->create(['name' => 'Adwa Kelas A', 'nickname' => 'AdwaA', 'class_name' => 'A']);
        Student::factory()->create(['name' => 'Adwa Kelas B', 'nickname' => 'AdwaB', 'class_name' => 'B']);

        $response = $this->call('GET', '/directory', ['search' => 'adw', 'class' => 'A'], [
            'angkatan_session' => $this->sessionCookie,
        ]);

        $response->assertOk();
        $response->assertSee('Adwa Kelas A');
        $response->assertDontSee('Adwa Kelas B');
        $response->assertSee('FILTER: KELAS A');
        $response->assertSee('KATA KUNCI: "adw"', false);
    }

    public function test_class_chips_keep_search_keyword_and_pagination_keeps_filters(): void
    {
        Student::factory()->count(20)->create(['class_name' => 'A', 'name' => 'Rekan Satu']);

        $response = $this->call('GET', '/directory', ['search' => 'rekan', 'class' => 'A'], [
            'angkatan_session' => $this->sessionCookie,
        ]);

        $response->assertOk();
        $response->assertSee('search=rekan&amp;class=A', false);
        $response->assertSee('search=rekan&amp;class=B', false);
        $response->assertSee('page=2', false);
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
