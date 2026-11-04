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

        // 1. Sub-Navigation & Breadcrumbs
        $response->assertSee('Kembali ke Direktori');
        $response->assertSee('// 04_DOSSIER_21120126130088');

        // 2. Candidate Card Header & Switcher (biodata-layout.png)
        $response->assertSee('// SEC_01 // CANDIDATE_CARD');
        $response->assertSee('[STATUS: ACTIVE_STUDENT]');
        $response->assertSee('VIEW_MODE:');
        $response->assertSee('Formal');
        $response->assertSee('Kasual');
        $response->assertSee('RATIO: 1000x1000 PX');
        $response->assertSee('JAS ALMAMATER UNDIP');
        $response->assertSee('FOTO BEBAS KASUAL');

        // 3. Core Identity, Class Badge, NIM, & Instagram
        $response->assertSee('Arkan');
        $response->assertSee('Muhammad Arkananta Pratama');
        $response->assertSee('NIM: 21120126130088');
        $response->assertSee('KELAS B // TEKKOM_26');
        $response->assertSee('@arkananta.p');

        // 4. Bio Quote Callout with REV_01 & Footer Hash
        $response->assertSee('// ASPIRASI & PRINSIP', false);
        $response->assertSee('REV_01');
        $response->assertSee('Konsistensi melampaui talenta tanpa disiplin.');
        $response->assertSee('RECORD_HASH:');
        $response->assertSee('CAMPUS_ID: UNDIP-FT-TK');

        // 5. Right Column Structured Metadata (biodata-layout.png)
        $response->assertSee('// 01_BIODATA_RESMI');
        $response->assertSee('Informasi Identitas & Kependudukan', false);
        $response->assertSee('VERIFIKASI_FAKULTAS: OK');
        $response->assertSee('Semarang');
        $response->assertSee('#DOMISILI');
        $response->assertSee('Surakarta');
        $response->assertSee('Islam');
        $response->assertSee('S1 Teknik Komputer, Fakultas Teknik');
        $response->assertSee('KAMPUS TEMBALANG, SEMARANG');

        // 6. Hobbies, Culinary Spot, & Spotify
        $response->assertSee('// MINAT & AKTIVITAS', false);
        $response->assertSee('[EXP: HARDWARE]');
        $response->assertSee('#Embedded Systems');
        $response->assertSee('#IoT Architecture');
        $response->assertSee('#Badminton');
        $response->assertSee('// SPOT KULINER FAVORIT');
        $response->assertSee('Warmindo Burjois');
        $response->assertSee('FAVORITE_SPOT');

        // 7. Spotify Embedded Player (dipertahankan)
        $response->assertSee('// ANTHEM_TRACK // SPOTIFY_EMBED');
        $response->assertSee('AUDIO_DEV_04');
        $response->assertSee('Lagu Favorit di Spotify');
        $response->assertSee('https://open.spotify.com/embed/track/7yCF65vpc7sUwPjftI0iFb?utm_source=generator', false);
        $response->assertSee('SYNC_OK');

        // 8. Log Verification Strip
        $response->assertSee('LOG: 2026-TEKKOM-DOSSIER-V4');
        $response->assertSee('+ CHECKSUM_VERIFIED');
    }
}
