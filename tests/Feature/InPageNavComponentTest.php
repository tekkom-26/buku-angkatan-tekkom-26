<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class InPageNavComponentTest extends TestCase
{
    public function test_in_page_nav_renders_collapsed_handle_and_expanded_drawer_with_default_items(): void
    {
        $html = Blade::render('<x-in-page-nav />');

        // 1. State 1: Collapsed Minimalist Handle
        $this->assertStringContainsString('[NAV]', $html);
        $this->assertStringContainsString('[X] [ICON: CHEVRON_LEFT]', $html);

        // 2. State 2: Expanded Header & Technical Brackets
        $this->assertStringContainsString('[PAGE NAV]', $html);
        $this->assertStringContainsString('[IN-PAGE]', $html);
        $this->assertStringContainsString('(ACTIVE)', $html);

        // 3. Default Bracketed Monospace Items
        $this->assertStringContainsString('[1. HERO]', $html);
        $this->assertStringContainsString('[2. STATS]', $html);
        $this->assertStringContainsString('[3. VALUES]', $html);
        $this->assertStringContainsString('[4. VISION]', $html);
        $this->assertStringContainsString('[5. DIRECTORY]', $html);

        // 4. Alpine.js State & Scroll Listener
        $this->assertStringContainsString('open: false', $html);
        $this->assertStringContainsString("activeSection: 'hero'", $html);
        $this->assertStringContainsString('updateActiveSection()', $html);
        $this->assertStringContainsString('scrollToSection(', $html);

        // 5. Strict Semantic Token Compliance (Zero Arbitrary HEX)
        $this->assertDoesNotMatchRegularExpression('/(?:bg|text|border|ring)-\[#[0-9a-fA-F]+\]/', $html);
    }

    public function test_in_page_nav_supports_custom_section_items(): void
    {
        $html = Blade::render('<x-in-page-nav :items="$items" />', [
            'items' => [
                ['id' => 'hero-section', 'label' => '[1. HERO]'],
                ['id' => 'decoding-section', 'label' => '[2. DECODING]'],
            ],
        ]);

        $this->assertStringContainsString("activeSection: 'hero-section'", $html);
        $this->assertStringContainsString('#hero-section', $html);
        $this->assertStringContainsString('#decoding-section', $html);
        $this->assertStringContainsString('[2. DECODING]', $html);
    }
}
