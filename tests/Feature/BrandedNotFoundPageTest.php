<?php

namespace Tests\Feature;

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
        $response->assertSee('Halaman ini tidak ditemukan.');
        $response->assertSee('Cari di Direktori');
        $response->assertSee(route('directory.index'), false);
    }

    public function test_missing_student_renders_branded_404(): void
    {
        $response = $this->get('/students/99999999');

        $response->assertNotFound();
        $response->assertSee('Halaman ini tidak ditemukan.');
        $response->assertSee('Cari nama atau NIM mahasiswa');
    }
}
