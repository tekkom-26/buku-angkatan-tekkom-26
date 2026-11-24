<?php

namespace Tests\Feature;

use App\Models\Student;
use Database\Seeders\AuthSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDetailBentoProfileTest extends TestCase
{
    use RefreshDatabase;

    private string $sessionCookie;

    protected function setUp(): void
    {
        parent::setUp();

        config(['angkatan.passcode' => 'secret-pass']);
        $this->seed(AuthSeeder::class);

        $cookie = $this->postJson('/api/auth/passcode', ['passcode' => 'secret-pass'])
            ->getCookie('angkatan_session', false);

        $this->sessionCookie = $cookie->getValue();
    }

    public function test_renders_student_detail_bento_profile_layout(): void
    {
        Student::factory()->create([
            'nim' => '21120126130088',
            'name' => 'Muhammad Arkananta Pratama',
            'nickname' => 'Arkan',
            'class_name' => 'B',
            'origin_city' => 'Semarang',
            'birth_place' => 'Surakarta',
            'religion' => 'Islam',
            'bio_quote' => 'Konsistensi melampaui talenta tanpa disiplin.',
            'hobbies' => 'Embedded Systems, IoT Architecture, Badminton',
            'favorite_food_place' => 'Warmindo Burjois',
            'instagram_username' => 'arkananta.p',
            'spotify_song_url' => 'https://open.spotify.com/track/7yCF65vpc7sUwPjftI0iFb?si=06036673457c41ad',
        ]);

        $response = $this->call('GET', '/students/21120126130088', [], [
            'angkatan_session' => $this->sessionCookie,
        ]);

        $response->assertOk();

        // 1. Sub-Navigation
        $response->assertSee('Kembali ke Direktori');

        // 2. Mode Switcher & Labels
        $response->assertSee('Mode Foto');
        $response->assertSee('Formal');
        $response->assertSee('Kasual');
        $response->assertSee('Jas Almamater');
        $response->assertSee('Foto Kasual');

        // 3. Core Identity, Class Badge, NIM, & Instagram
        $response->assertSee('Arkan');
        $response->assertSee('Muhammad Arkananta Pratama');
        $response->assertSee('NIM: 21120126130088');
        $response->assertSee('Kelas B');
        $response->assertSee('@arkananta.p');

        // 4. Bio Quote
        $response->assertSee('Konsistensi melampaui talenta tanpa disiplin.');

        // 5. Right Column Structured Metadata
        $response->assertSee('Informasi Identitas &amp; Kependudukan', false);
        $response->assertSee('Semarang');
        $response->assertSee('Surakarta');
        $response->assertSee('Islam');
        $response->assertSee('S1 Teknik Komputer, Fakultas Teknik');
        $response->assertSee('Kampus Tembalang, Semarang');

        // 6. Hobbies & Culinary Spot
        $response->assertSee('#Embedded Systems');
        $response->assertSee('#IoT Architecture');
        $response->assertSee('#Badminton');
        $response->assertSee('Warmindo Burjois');

        // 7. Spotify Embedded Player
        $response->assertSee('Lagu Favorit di Spotify');
        $response->assertSee('https://open.spotify.com/embed/track/7yCF65vpc7sUwPjftI0iFb?utm_source=generator', false);
    }
}
