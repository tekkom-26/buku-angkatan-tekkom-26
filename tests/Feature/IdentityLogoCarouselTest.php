<?php

namespace Tests\Feature;

use Tests\TestCase;

class IdentityLogoCarouselTest extends TestCase
{
    public function test_identity_page_renders_logo_symbolism_carousel_with_all_five_comets_layers(): void
    {
        $response = $this->get('/identity');
        $response->assertOk();
        $response->assertViewIs('identity');

        // Header & Counter
        $response->assertSee('Anatomi Lambang');
        $response->assertSee('Makna Elemen');
        $response->assertSee('/ 05');

        // 5 COMETS Logo Elements
        $response->assertSee('Lingkaran — Kesetaraan Tanpa Awal &amp; Akhir', false);
        $response->assertSee('Lebah — Satu Koloni Membangun Sarang Kokoh');
        $response->assertSee('Sayap Bersirkuit — Rekayasa &amp; Daya Juang', false);
        $response->assertSee('Belang &amp; Sengat — Terang, Gelap &amp; Daya Tahan', false);
        $response->assertSee('26 Segi Enam — 26 Simpul Rumah Bersama');

        // Navigation Controls & Alpine State
        $response->assertSee('Pilih slide komponen logo');
        $response->assertSee('Slide logo sebelumnya');
        $response->assertSee('Slide logo berikutnya');
        $response->assertSee('activeSlide: 1');
        $response->assertSee('totalSlides: 5');
    }

    public function test_identity_page_renders_all_five_sections_of_screen_5(): void
    {
        $response = $this->get('/identity');
        $response->assertOk();
        $response->assertViewIs('identity');

        // Section 1: Header Banner
        $response->assertSee('Anatomi Identitas COMETS');
        $response->assertSee('Foto Kolaboratif Angkatan');

        // Section 2: Visi & Misi
        $response->assertSee('Arah Gerak &amp; Komitmen Angkatan', false);
        $response->assertSee('SOLID LIKE A BEE, BE LIKE WE WANT TO BE, STRONG AND FREE!');
        $response->assertSee('Visi Utama');
        $response->assertSee('Misi Angkatan');

        // Section 3, 4, 5 Headings
        $response->assertSee('Bedah Simbolisme');
        $response->assertSee('Bedah Karakter');
        $response->assertSee('Tiga Pilar Warna Resmi');
    }

    public function test_logo_carousel_does_not_contain_arbitrary_hex_in_classes(): void
    {
        $response = $this->get('/identity');
        $content = $response->getContent();
        $this->assertDoesNotMatchRegularExpression('/class="[^"]*#[0-9a-fA-F]{3,8}[^"]*"/', $content);
        $this->assertStringNotContainsString('bg-[#', $content);
        $this->assertStringNotContainsString('text-[#', $content);
        $this->assertStringNotContainsString('border-[#', $content);
    }
}
