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
        $response->assertSee('ASCENT // TEKKOM 2026');

        // 2. Official Motto
        $response->assertSee('Integrity, Unbreakable, We Are Competent!!!');

        // 3. CTA Buttons
        $response->assertSee('Jelajahi Direktori Angkatan');
        $response->assertSee('Baca Filosofi Lambang');

        // 4. Right Card Status Badge & Specs
        $response->assertSee('2026]');
        $response->assertSee('TEKNIK KOMPUTER');
        $response->assertSee('FT UNIVERSITAS DIPONEGORO');
        $response->assertSee('100+ STUDENTS');

        // 5. Mascot Placeholder Image
        $response->assertSee('/assets/Placeholder.png');
    }
}
