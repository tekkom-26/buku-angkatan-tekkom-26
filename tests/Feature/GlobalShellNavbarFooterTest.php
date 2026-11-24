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

        // Navbar Brand & Links
        $response->assertSee('COMETS 2026');
        $response->assertSee('Beranda');
        $response->assertSee('Identitas');
        $response->assertSee('Direktori');
        $response->assertSee('Galeri');

        // Guest Action CTA
        $response->assertSee('Masuk untuk akses direktori');
        $response->assertSee(route('unlock'), false);

        // Clean Footer Components (no fake // telemetry)
        $response->assertSee('Teknik Komputer 2026 &mdash; Universitas Diponegoro', false);
        $response->assertSee('Departemen Teknik Komputer, Fakultas Teknik, Universitas Diponegoro');
        $response->assertSee('Buka di Google Maps');
        $response->assertSee('Navigasi');
        $response->assertSee('Kanal Resmi');
        $response->assertSee('Instagram (@tekkom26)');
        $response->assertSee('Website Departemen');
        $response->assertDontSee('// NAVIGASI');
        $response->assertDontSee('// KANAL_RESMI');
    }

    public function test_unlock_page_hides_redundant_login_cta_and_directory_active_state(): void
    {
        $response = $this->get('/unlock');

        $response->assertOk();
        $response->assertSee('Akses Direktori');
        $response->assertDontSee('aria-label="Masuk untuk akses direktori"', false);
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
        $response->assertSee('Beranda');
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
