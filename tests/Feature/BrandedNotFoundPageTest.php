<?php

namespace Tests\Feature;

use Database\Seeders\AuthSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandedNotFoundPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_url_renders_branded_404_with_directory_search(): void
    {
        $response = $this->get('/unknown-page');
        $response->assertNotFound();
        $response->assertSee('Tekkom 26');
        $response->assertSee('Error 404');
        $response->assertSee('Halaman ini tidak ditemukan.');
        $response->assertSee('Cari di Direktori');
        $response->assertSee('Kembali ke Beranda Utama');
        $response->assertSee('Jelajahi Direktori Mahasiswa');
        $response->assertSee('Rute Tidak Tersedia');
        $response->assertSee(route('landing'), false);
        $response->assertSee(route('directory.index'), false);
        $this->assertDoesNotMatchRegularExpression('/(?:bg|text|border|ring|fill|stroke)-\[#[0-9a-fA-F]+\]/', $response->getContent());
    }

    public function test_missing_student_renders_branded_404(): void
    {
        config(['angkatan.passcode' => 'secret-pass']);
        $this->seed(AuthSeeder::class);

        $cookie = $this->postJson('/api/auth/passcode', ['passcode' => 'secret-pass'])
            ->getCookie('angkatan_session', false);

        $response = $this->call('GET', '/students/99999999', [], [
            'angkatan_session' => $cookie->getValue(),
        ]);

        $response->assertNotFound();
        $response->assertSee('Halaman ini tidak ditemukan.');
        $response->assertSee('Cari nama atau NIM mahasiswa');
    }
}
