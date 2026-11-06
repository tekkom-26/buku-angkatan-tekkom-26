<?php

namespace Tests\Feature;

use App\Models\Student;
use Database\Seeders\AuthSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalShellNavbarFooterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['angkatan.passcode' => 'secret-pass']);
        $this->seed(AuthSeeder::class);
    }

    public function test_navbar_and_footer_render_on_landing_as_guest(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        // Navbar Brand & Links (stich-uiux/Navbar.png)
        $response->assertSee('ANGKATAN // TEKKOM 2026');
        $response->assertSee('Beranda');
        $response->assertSee('Filosofi &amp; Identitas', false);
        $response->assertSee('Direktori');
        $response->assertSee('Galeri');

        // Guest Action CTA
        $response->assertSee('Akses Direktori');

        // Footer Components (stich-uiux/Footer.png)
        $response->assertSee('GAJAH_CHIBI_TEKKOM');
        $response->assertSee('Maskot Resmi Angkatan 2026');
        $response->assertSee('ANGKATAN &mdash; Teknik Komputer 2026', false);
        $response->assertSee('Departemen Teknik Komputer, Fakultas Teknik, Universitas Diponegoro');
        $response->assertSee('Google Maps');
        $response->assertSee('// NAVIGASI');
        $response->assertSee('// KANAL_RESMI');
        $response->assertSee('Instagram (@tekkom26)');
        $response->assertSee('// TELEMETRI_SISTEM');
        $response->assertSee('[BATCH_STATUS: ACTIVE]');
        $response->assertSee('[ANGKATAN_SPEC: TEKKOM_XXVI]');
        $response->assertSee('[SECURITY_LEVEL: VERIFIED]');
        $response->assertSee('[STUDENT_COUNT: 104_STUDENTS]');
        $response->assertSee('ANGKATAN Teknik Komputer Undip. All rights reserved.');
    }

    public function test_navbar_renders_back_button_and_logout_on_directory_when_authenticated(): void
    {
        Student::factory()->create();

        $cookie = $this->postJson('/api/auth/passcode', ['passcode' => 'secret-pass'])
            ->getCookie('angkatan_session', false);

        $response = $this->call('GET', '/directory', [], [
            'angkatan_session' => $cookie->getValue(),
        ]);

        $response->assertOk();

        // In Directory Context, Navbar shows "Kembali Ke Beranda"
        $response->assertSee('Kembali Ke Beranda');

        // And authenticated shows the logout/relock action
        $response->assertSee(route('lock'));
    }

    public function test_lock_route_clears_session_and_redirects_to_landing(): void
    {
        $cookie = $this->postJson('/api/auth/passcode', ['passcode' => 'secret-pass'])
            ->getCookie('angkatan_session', false);

        $response = $this->call('POST', '/lock', [
            '_token' => csrf_token(),
        ], [
            'angkatan_session' => $cookie->getValue(),
        ]);

        $response->assertRedirectToRoute('landing');
        $response->assertCookieExpired('angkatan_session');
    }
}
