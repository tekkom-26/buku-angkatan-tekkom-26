<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingDirectoryBannerTest extends TestCase
{
    public function test_landing_page_renders_directory_quick_discovery_banner_plate(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewIs('landing');

        // 2. Callout Headline & Description Copy
        $response->assertSee('Temukan Rekan Satu Angkatan di Direktori Resmi');
        $response->assertSee('Akses profil lengkap mahasiswa Teknik Komputer 2026 berdasarkan kelas, domisili, dan minat eksplorasi melalui sistem direktori terverifikasi.');

        // 3. Primary Sky Wash CTA Button & Route Binding
        $response->assertSee('Jelajahi 100+ Profil Mahasiswa');
        $response->assertSee('href="'.route('directory.index').'"', false);
        $response->assertSee('KELAS A · KELAS B · KELAS C · KELAS D');
    }

    public function test_directory_banner_component_does_not_contain_arbitrary_hex_in_classes(): void
    {
        $response = $this->get('/');
        $content = $response->getContent();

        $this->assertDoesNotMatchRegularExpression('/class="[^"]*#[0-9a-fA-F]{3,8}[^"]*"/', $content);
        $this->assertStringNotContainsString('bg-[#', $content);
        $this->assertStringNotContainsString('text-[#', $content);
        $this->assertStringNotContainsString('border-[#', $content);
    }
}
