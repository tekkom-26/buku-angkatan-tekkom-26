<?php

namespace Tests\Feature;

use Tests\TestCase;

class IdentityColorPillarsTest extends TestCase
{
    public function test_identity_page_renders_color_pillars_section_with_all_four_swatches(): void
    {
        $response = $this->get('/identity');

        $response->assertOk();
        $response->assertViewIs('identity');

        // Section Header
        $response->assertSee('// 02_COLOR_SYSTEM');
        $response->assertSee('Empat Pilar Warna');
        $response->assertSee('PALETTE // 04_SEMANTIC_TOKENS');

        // Pillar 1: Sky Wash
        $response->assertSee('PILLAR_01');
        $response->assertSee('Sky Wash');
        $response->assertSee('Primary Interactive');
        $response->assertSee('#80bab2');
        $response->assertSee('--color-secondary-500');

        // Pillar 2: Butter Cream
        $response->assertSee('PILLAR_02');
        $response->assertSee('Butter Cream');
        $response->assertSee('Accent & Alert');
        $response->assertSee('#d4d86a');
        $response->assertSee('--color-highlight-500');

        // Pillar 3: Deep Plum
        $response->assertSee('PILLAR_03');
        $response->assertSee('Deep Plum');
        $response->assertSee('Structural Grounding');
        $response->assertSee('#514051');
        $response->assertSee('--color-primary-900');

        // Pillar 4: Snow White
        $response->assertSee('PILLAR_04');
        $response->assertSee('Snow White');
        $response->assertSee('Surface Canvas');
        $response->assertSee('#e6e3e8');
        $response->assertSee('--color-surface-card');
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
