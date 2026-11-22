<?php

namespace Tests\Feature;

use Tests\TestCase;

class IdentityColorPillarsTest extends TestCase
{
    public function test_identity_page_renders_color_pillars_section_with_all_three_comets_swatches_and_chant(): void
    {
        $response = $this->get('/identity');

        $response->assertOk();
        $response->assertViewIs('identity');

        // Section Header
        $response->assertSee('// 02_COLOR_SYSTEM');
        $response->assertSee('Tiga Pilar Warna Resmi');
        $response->assertSee('PALETTE // 03_SEMANTIC_TOKENS');

        // Pillar 1: Charcoal
        $response->assertSee('PILLAR_01');
        $response->assertSee('Charcoal');
        $response->assertSee('Kebersamaan');
        $response->assertSee('#252525');
        $response->assertSee('--color-surface-card');

        // Pillar 2: Tech Gold
        $response->assertSee('PILLAR_02');
        $response->assertSee('Tech Gold');
        $response->assertSee('Potensi Diri');
        $response->assertSee('#D4AF37');
        $response->assertSee('--color-brand-gold');

        // Pillar 3: Blood Red
        $response->assertSee('PILLAR_03');
        $response->assertSee('Blood Red');
        $response->assertSee('Ketangguhan');
        $response->assertSee('#690F1D');
        $response->assertSee('--color-brand-red');

        // Audio Card & 3-Stanza Yel-Yel Chants
        $response->assertSee('// COLONY_CHANT // AUDIO_CARD');
        $response->assertSee('SOLID LIKE A BEE, BE LIKE WE WANT TO BE, STRONG AND FREE!');
        $response->assertSee('Ada cerita tentang sebuah canda dan tawa');
        $response->assertSee('Inilah kami Comets');
        $response->assertSee('Akan kami buktikan!');
    }

    public function test_color_pillars_section_does_not_contain_arbitrary_hex_in_classes(): void
    {
        $response = $this->get('/identity');
        $content = $response->getContent();

        $this->assertDoesNotMatchRegularExpression('/class="[^"]*#[0-9a-fA-F]{3,8}[^"]*"/', $content);
        $this->assertStringNotContainsString('bg-[#', $content);
        $this->assertStringNotContainsString('text-[#', $content);
        $this->assertStringNotContainsString('border-[#', $content);
    }
}
