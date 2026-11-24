<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingHeroSectionTest extends TestCase
{
    public function test_landing_page_renders_hero_asymmetric_bento_grid(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        // 1. Clean Hero Heading & Subtitle
        $response->assertSee('Teknik Komputer · FT Undip · 2026');
        $response->assertSee('COMETS 2026');

        // 2. Primary Actions
        $response->assertSee('Jelajahi Direktori');
        $response->assertSee('Tentang Identitas');
    }

    public function test_landing_page_renders_full_screen_one_sections_and_in_page_nav(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        // Section 2: Values & Mascot
        $response->assertSee('Jargon &amp; Nilai Inti', false);
        $response->assertSee('SOLID LIKE A BEE, BE LIKE WE WANT TO BE, STRONG AND FREE!');
        $response->assertSee('Pelajari Filosofi &amp; Identitas', false);

        // Section 3: Moments Carousel
        $response->assertSee('Dokumentasi &amp; Kilas Balik Angkatan', false);
        $response->assertSee('Inaugurasi &amp; Pengukuhan Mahasiswa Baru Teknik Komputer 2026', false);

        // Section 4: Directory Banner
        $response->assertSee('Member Angkatan 2026');
        $response->assertSee('Buka Direktori');

        // Original In-Page Side Navigation Drawer
        $response->assertSee('[PAGE NAV]');
        $response->assertSee('[IN-PAGE]');
        $response->assertSee('[1. HERO]');
        $response->assertSee('[2. DECODING]');
        $response->assertSee('[3. MOMENTS]');
        $response->assertSee('[4. DIRECTORY]');

        // Strict compliance: no arbitrary hex in new landing components
        $this->assertDoesNotMatchRegularExpression('/(?:bg|text|border|ring)-\[#[0-9a-fA-F]+\]/', $response->getContent());
    }
}
