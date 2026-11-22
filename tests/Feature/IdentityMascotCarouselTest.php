<?php

namespace Tests\Feature;

use Tests\TestCase;

class IdentityMascotCarouselTest extends TestCase
{
    public function test_identity_page_renders_successfully(): void
    {
        $response = $this->get('/identity');

        $response->assertOk();
        $response->assertViewIs('identity');
    }

    public function test_identity_page_renders_mascot_anatomy_carousel_with_all_five_beener_attributes(): void
    {
        $response = $this->get('/identity');

        $response->assertOk();

        // 1. Technical Index & Header
        $response->assertSee('// 04_MASCOT_ANATOMY');
        $response->assertSee('MASCOT / 05 ATTRIBUTES');

        // 2. Attribute Breakdown Header & Slide Counter Badge
        $response->assertSee('ATTRIBUTE BREAKDOWN');
        $response->assertSee('[ KOMPONEN');
        $response->assertSee('/ 05 ]');

        // 3. 5 Bee-ner Mascot Attributes (PRD §3.2.4)
        $response->assertSee('Strip Hitam-Emas // Logika Biner 0 & 1');
        $response->assertSee('STRIP HITAM-EMAS');
        $response->assertSee('Mata Hexagon // Potensi Diri yang Menyala');
        $response->assertSee('MATA HEXAGON');
        $response->assertSee('Ring Bahu // Kuat Memikul Beban Bersama');
        $response->assertSee('RING BAHU');
        $response->assertSee('Antena // Peka & Peduli Terhadap Sesama');
        $response->assertSee('ANTENA');
        $response->assertSee('Senyum Ramah // Inklusivitas & Keterbukaan Koloni');
        $response->assertSee('SENYUM RAMAH');

        // 4. Navigation Controls & Alpine State
        $response->assertSee('Pilih slide maskot');
        $response->assertSee('Slide sebelumnya');
        $response->assertSee('Slide berikutnya');
        $response->assertSee('activeSlide: 1');
        $response->assertSee('totalSlides: 5');
        $response->assertSee('/assets/Placeholder.png');
    }

    public function test_mascot_carousel_does_not_contain_arbitrary_hex_in_classes(): void
    {
        $response = $this->get('/identity');
        $content = $response->getContent();

        $this->assertDoesNotMatchRegularExpression('/class="[^"]*#[0-9a-fA-F]{3,8}[^"]*"/', $content);
        $this->assertStringNotContainsString('bg-[#', $content);
        $this->assertStringNotContainsString('text-[#', $content);
        $this->assertStringNotContainsString('border-[#', $content);
    }
}
