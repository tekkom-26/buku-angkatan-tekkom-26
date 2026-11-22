<x-layouts.app activePage="identity" :title="'Identitas Angkatan — ' . $cohortName">
    <x-in-page-nav
        :items="[
            ['id' => 'identity-header-section', 'label' => '[1. HEADER]'],
            ['id' => 'identity-vision-mission-section', 'label' => '[2. VISI & MISI]'],
            ['id' => 'identity-logo-section', 'label' => '[3. LOGO]'],
            ['id' => 'identity-mascot-section', 'label' => '[4. MASCOT]'],
            ['id' => 'identity-color-pillars-section', 'label' => '[5. COLORS & CHANT]'],
        ]"
    />

    <main id="main-content" class="grow">
        {{-- Section 1: Header Banner — Anatomi Identitas COMETS (PRD §3.2.1) --}}
        <x-identity.header-banner :content="$identityContent['header_banner'] ?? null" />

        {{-- Section 2: Visi & Misi Bento (PRD §3.2.2) --}}
        <x-identity.vision-mission :content="$identityContent['vision_mission'] ?? null" />

        {{-- Section 3: Logo Explorer — 5 Elemen Logo COMETS (PRD §3.2.3) --}}
        <x-identity.logo-carousel :content="$identityContent['logo_symbolism'] ?? null" />

        {{-- Section 4: Mascot Explorer — 5 Atribut Bee-ner (PRD §3.2.4) --}}
        <x-identity.mascot-carousel :content="$identityContent['mascot'] ?? null" />

        {{-- Section 5: Color Pillars (3 Warna Resmi) & Chants/Yel-Yel (PRD §3.2.5) --}}
        <x-identity.color-pillars :content="$identityContent['color_pillars'] ?? null" />
    </main>
</x-layouts.app>
