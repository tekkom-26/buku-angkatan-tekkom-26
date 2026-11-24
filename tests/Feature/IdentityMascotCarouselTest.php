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

        // Header & Counter
        $response->assertSee('Karakter Maskot');
        $response->assertSee('Detail Karakter');
        $response->assertSee('/ 05');

        // 5 Bee-ner Mascot Attributes
        $response->assertSee('Strip Hitam-Emas — Logika Biner 0 &amp; 1', false);
        $response->assertSee('Mata Hexagon — Potensi yang Menyala');
        $response->assertSee('Ring Bahu — Kuat Memikul Beban Bersama');
        $response->assertSee('Antena — Peka &amp; Peduli Sesama', false);
        $response->assertSee('Senyum Ramah — Inklusivitas Koloni');

        // Navigation Controls & Alpine State
        $response->assertSee('Pilih slide maskot');
        $response->assertSee('Slide sebelumnya');
        $response->assertSee('Slide berikutnya');
        $response->assertSee('activeSlide: 1');
        $response->assertSee('totalSlides: 5');
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
