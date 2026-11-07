<?php

namespace Tests\Feature;

use Tests\TestCase;

class IdentityLogoCarouselTest extends TestCase
{
    public function test_identity_page_renders_logo_symbolism_carousel_with_all_layers(): void
    {
        $response = $this->get('/identity');
        $response->assertOk();
        $response->assertViewIs('identity');
        // 1. Technical Index & Header
        $response->assertSee('// 03_LOGO_ANATOMY');
        $response->assertSee('Bedah Simbolisme');
        $response->assertSee('LOGO / 04 LAYERS');
        // 2. Layer Breakdown Header & Slide Counter Badge
        $response->assertSee('LAYER BREAKDOWN');
        $response->assertSee('[ KOMPONEN');
        $response->assertSee('/ 04 ]');
        $response->assertSee('ASSET_LAYER: 01_OF_04 // VECTOR_CONSTRUCT');
        // 3. Slide 1: Sayap Angkatan
        $response->assertSee('Sayap Angkatan // Elevasi & Ambisi Melambung');
        $response->assertSee('Elevasi // Ambisi Melambung');
        $response->assertSee('WINGS');
        $response->assertSee('[X] [ASSET: LOGO_COMPONENT_1:1 — ELEMEN_SAYAP]');
        // 4. Slide 2: Geometri Angka 26
        $response->assertSee('Geometri Angka 26 // Identitas Angkatan');
        $response->assertSee('Identitas // Angkatan 2026');
        $response->assertSee('NUMERAL 26');
        $response->assertSee('[X] [ASSET: LOGO_COMPONENT_1:1 — GEOMETRI_ANGKA_26]');
        // 5. Slide 3: Sirkuit Logika Komputasi
        $response->assertSee('Sirkuit Logika Komputasi // Keahlian Rekayasa');
        $response->assertSee('Keahlian // Rekayasa Komputasi');
        $response->assertSee('CIRCUIT');
        $response->assertSee('[X] [ASSET: LOGO_COMPONENT_1:1 — SIRKUIT_LOGIKA_KOMPUTASI]');
        // 6. Slide 4: Lambang Utuh Terintegrasi
        $response->assertSee('Lambang Utuh Terintegrasi // Solidaritas Kolektif');
        $response->assertSee('Solidaritas // Kesatuan Kolektif');
        $response->assertSee('FULL EMBLEM');
        $response->assertSee('[X] [ASSET: LOGO_COMPONENT_1:1 — LAMBANG_UTUH_TERINTEGRASI]');

        // 7. Navigation Controls & Alpine State
        $response->assertSee('Pilih slide komponen logo');
        $response->assertSee('Slide logo sebelumnya');
        $response->assertSee('Slide logo berikutnya');
        $response->assertSee('activeSlide: 1');
        $response->assertSee('totalSlides: 4');
        $response->assertSee('/assets/Placeholder.png');
    }

    public function test_identity_page_renders_all_five_sections_of_screen_5(): void
    {
        $response = $this->get('/identity');
        $response->assertOk();
        $response->assertViewIs('identity');

        // Section 1: Editorial Header & 21:9 Batch Hero Banner (Issue #22)
        $response->assertSee('// 05_IDENTITY — FILOSOFI & SIMBOLISME //', false);
        $response->assertSee('Identitas, Filosofi & Nilai Dasar');
        $response->assertSee('[X] [IMAGE: BATCH_HERO_BANNER_21:9 — FOTO_KOLABORATIF_ANGKATAN]');
        $response->assertSee('[ARCHIVE_REF: ID_BANNER_2026] | ASPECT_RATIO: 21:9');
        $response->assertSee('BATCH_XXVI // SPECIFICATION');

        // Section 2: Visi & Misi Asymmetric 2-Column Bento Grid (Issue #23)
        $response->assertSee('// 01_VISION_MISSION');
        $response->assertSee('Arah Gerak & Komitmen Angkatan', false);
        $response->assertSee('Integrity, Unbreakable, We Are Competent!!!');
        $response->assertSee('[ VISI_UTAMA ]');
        $response->assertSee('[ MISI_EKSEKUSI ]');
        $response->assertSee('Solidaritas & Inklusivitas Tanpa Sekat');
        $response->assertSee('Akselerasi Kompetensi Akademik & Rekayasa');
        $response->assertSee('Integritas Karakter & Kontribusi Berkelanjutan');

        // Section 3: 4 Official Color Pillars Horizontal Swatch Grid (Issue #24)
        $response->assertSee('// 02_COLOR_SYSTEM');
        $response->assertSee('Empat Pilar Warna');
        $response->assertSee('PALETTE // 04_SEMANTIC_TOKENS');
        $response->assertSee('Sky Wash');
        $response->assertSee('Butter Cream');
        $response->assertSee('Deep Plum');
        $response->assertSee('Snow White');

        // Section 4 & Section 5 headers
        $response->assertSee('// 03_LOGO_ANATOMY');
        $response->assertSee('// 04_MASCOT_ANATOMY');
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
