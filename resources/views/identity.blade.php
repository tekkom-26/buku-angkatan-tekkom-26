<x-layouts.app activePage="identity" title="Identitas Angkatan — Teknik Komputer 2026">
    <main id="main-content" class="flex-grow">
        {{-- Section 1: Editorial Header & 21:9 Batch Hero Banner (Issue #22) --}}
        <x-identity.header-banner :content="$identityContent['header_banner'] ?? null" />

        {{-- Section 2: Visi & Misi Asymmetric 2-Column Bento Grid (Issue #23) --}}
        <x-identity.vision-mission :content="$identityContent['vision_mission'] ?? null" />

        {{-- Section 3: 4 Official Color Pillars Horizontal Swatch Grid (Issue #24) --}}
        <x-identity.color-pillars :content="$identityContent['color_pillars'] ?? null" />

        {{-- Section 4: Logo Symbolism Split Carousel (50/50 & Layer Breakdown) (Issue #25) --}}
        <x-identity.logo-carousel :content="$identityContent['logo_symbolism'] ?? null" />

        {{-- Section 5: Mascot Anatomy Split Carousel (50/50 & Attribute Breakdown) (Issue #26) --}}
        <x-identity.mascot-carousel :content="$identityContent['mascot'] ?? null" />
    </main>
</x-layouts.app>
