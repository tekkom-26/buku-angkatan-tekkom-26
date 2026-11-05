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

    public function test_identity_page_renders_mascot_anatomy_carousel_with_all_attributes(): void
    {
        $response = $this->get('/identity');

        $response->assertOk();

        // 1. Technical Index & Header
        $response->assertSee('// 04_MASCOT_ANATOMY');
        $response->assertSee('Bedah Karakter');
        $response->assertSee('& Anatomi Maskot', false);
        $response->assertSee('MASCOT / 04 ATTRIBUTES');

        // 2. Attribute Breakdown Header & Slide Counter Badge
        $response->assertSee('ATTRIBUTE BREAKDOWN');
        $response->assertSee('[ KOMPONEN');
        $response->assertSee('/ 04 ]');

        // 3. Slide 1: Gading Komputasi
        $response->assertSee('Gading Komputasi');
        $response->assertSee('Memori // Daya Ingat Kuat');
        $response->assertSee('TUSK');
        $response->assertSee('[X] [ASSET: MASCOT_COMPONENT_1:1 — ANATOMI_GADING]');

        // 4. Slide 2: Belalai Ramah
        $response->assertSee('Belalai Ramah');
        $response->assertSee('Sinergi // Komunikasi Tanpa Sekat');
        $response->assertSee('TRUNK');
        $response->assertSee('[X] [ASSET: MASCOT_COMPONENT_1:1 — ANATOMI_BELALAI]');

        // 5. Slide 3: Almamater / Hardware
        $response->assertSee('Almamater / Hardware');
        $response->assertSee('Identitas // Teknik Komputer');
        $response->assertSee('JACKET');
        $response->assertSee('[X] [ASSET: MASCOT_COMPONENT_1:1 — ANATOMI_ALMAMATER]');

        // 6. Slide 4: Pose Penuh Karakter Chibi
        $response->assertSee('Pose Penuh Karakter');
        $response->assertSee('Semangat // Angkatan 2026');
        $response->assertSee('FULL BODY');
        $response->assertSee('[X] [ASSET: MASCOT_COMPONENT_1:1 — POSE_LENGKAP_CHIBI]');

        // 7. Navigation Controls
        $response->assertSee('Pilih slide maskot');
        $response->assertSee('Slide sebelumnya');
        $response->assertSee('Slide berikutnya');

        // 8. Alpine.js State & Placeholder Image
        $response->assertSee('activeSlide: 1');
        $response->assertSee('totalSlides: 4');
        $response->assertSee('/assets/Placeholder.png');
    }

    public function test_mascot_carousel_does_not_contain_arbitrary_hex_in_classes(): void
    {
        $response = $this->get('/identity');
        $content = $response->getContent();

        // Ensure no arbitrary hex in class attributes
        $this->assertDoesNotMatchRegularExpression('/class="[^"]*#[0-9a-fA-F]{3,8}[^"]*"/', $content);
        $this->assertStringNotContainsString('bg-[#', $content);
        $this->assertStringNotContainsString('text-[#', $content);
        $this->assertStringNotContainsString('border-[#', $content);
    }
}
