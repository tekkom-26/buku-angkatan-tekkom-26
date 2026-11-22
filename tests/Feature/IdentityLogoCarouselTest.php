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

        // 1. Technical Index & Header
        $response->assertSee('// 03_LOGO_ANATOMY');
        $response->assertSee('LOGO / 05 LAYERS');

        // 2. Layer Breakdown Header & Slide Counter Badge
        $response->assertSee('LAYER BREAKDOWN');
        $response->assertSee('[ KOMPONEN');
        $response->assertSee('/ 05 ]');
        $response->assertSee('ASSET_LAYER: 01_OF_05 // COMETS_VECTOR');

        // 3. 5 COMETS Logo Elements (PRD §3.2.3)
        $response->assertSee('Lingkaran // Kesetaraan Tanpa Awal & Akhir');
        $response->assertSee('LINGKARAN');
        $response->assertSee('Lebah // Satu Koloni Membangun Sarang Kokoh');
        $response->assertSee('LEBAH');
        $response->assertSee('Sayap Bersirkuit // Identitas Teknik Komputer & Daya Juang');
        $response->assertSee('SAYAP BERSIRKUIT');
        $response->assertSee('Belang & Sengat // Masa Terang-Gelap & Ketangguhan');
        $response->assertSee('BELANG & SENGAT');
        $response->assertSee('26 Segi Enam // 26 Simpul Terhubung Membentuk Rumah Bersama');
        $response->assertSee('26 SEGI ENAM');

        // 4. Navigation Controls & Alpine State
        $response->assertSee('Pilih slide komponen logo');
        $response->assertSee('Slide logo sebelumnya');
        $response->assertSee('Slide logo berikutnya');
        $response->assertSee('activeSlide: 1');
        $response->assertSee('totalSlides: 5');
        $response->assertSee('/assets/Placeholder.png');
    }

    public function test_identity_page_renders_all_five_sections_of_screen_5(): void
    {
        $response = $this->get('/identity');
        $response->assertOk();
        $response->assertViewIs('identity');

        // Section 1: Header Banner
        $response->assertSee('// 05_IDENTITY — FILOSOFI &amp; SIMBOLISME //', false);
        $response->assertSee('Anatomi Identitas COMETS');
        $response->assertSee('[X] [IMAGE: BATCH_HERO_BANNER_21:9 — FOTO_KOLABORATIF_ANGKATAN]');
        $response->assertSee('[ARCHIVE_REF: ID_BANNER_2026] | ASPECT_RATIO: 21:9');
        $response->assertSee('BATCH_XXVI // SPECIFICATION');

        // Section 2: Visi & Misi Bento Grid
        $response->assertSee('// 01_VISION_MISSION');
        $response->assertSee('Arah Gerak &amp; Komitmen Angkatan', false);
        $response->assertSee('SOLID LIKE A BEE, BE LIKE WE WANT TO BE, STRONG AND FREE!');
        $response->assertSee('[ VISI_UTAMA ]');
        $response->assertSee('[ MISI_EKSEKUSI ]');

        // Section 3, 4, 5 Headers
        $response->assertSee('// 03_LOGO_ANATOMY');
        $response->assertSee('// 04_MASCOT_ANATOMY');
        $response->assertSee('// 02_COLOR_SYSTEM');
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
