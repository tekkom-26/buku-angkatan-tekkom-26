<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingHeroSectionTest extends TestCase
{
    public function test_landing_page_renders_hero_asymmetric_bento_grid(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        // 1. Technical Index & Headline
        $response->assertSee('// 01_HERO // BATCH_XXVI');
        $response->assertSee('ANGKATAN // TEKKOM 2026');

        // 2. Official Motto
        $response->assertSee('LOREM IPSUM DOLOR!!!');

        // 3. CTA Buttons
        $response->assertSee('Jelajahi Direktori Angkatan');
        $response->assertSee('Baca Filosofi Lambang');

        // 4. Right Card Status Badge & Specs
        $response->assertSee('2026]');
        $response->assertSee('TEKNIK KOMPUTER');
        $response->assertSee('FT UNIVERSITAS DIPONEGORO');
        $response->assertSee('205 STUDENTS');

        // 5. Mascot Placeholder Image
        $response->assertSee('/assets/Placeholder.png');
    }

    public function test_landing_page_renders_full_screen_one_sections_and_in_page_nav(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        // Section 2: Decoding ASCENT
        $response->assertSee('ASCENT');
        $response->assertSee('As One Computer Engineering Twenty Six');
        $response->assertSee('// 01_THE_ASCENT');
        $response->assertSee('// 02_AS_ONE');
        $response->assertSee('[ASSET: LOGO_XXVI_PRIMARY_1:1]');

        // Section 3: Moments & Highlights Carousel
        $response->assertSee('// 03_ARCHIVE // MOMENTS');
        $response->assertSee('Dokumentasi & Kilas Balik Angkatan', false);
        $response->assertSee('[X] [PHOTO_MOMENT_16:9 — INAUGURATION_EVENT]');
        $response->assertSee('[X] [PHOTO_MOMENT_16:9 — HARDWARE_LAB_SESSION]');
        $response->assertSee('[X] [PHOTO_MOMENT_16:9 — COHORT_GATHERING]');

        // Section 4: Directory Quick Discovery Banner Plate
        $response->assertSee('// QUICK_DISCOVERY');
        $response->assertSee('Jelajahi 100+ Profil Mahasiswa');

        // In-Page Side Navigation Drawer
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
