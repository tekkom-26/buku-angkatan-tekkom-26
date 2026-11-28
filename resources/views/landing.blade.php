<x-layouts.app activePage="home" :title="$cohortName . ' — Buku Angkatan'">
    <x-in-page-nav
        :items="[
            ['id' => 'hero-section', 'label' => '[1. HERO]'],
            ['id' => 'decoding-section', 'label' => '[2. DECODING]'],
            ['id' => 'moments-section', 'label' => '[3. MOMENTS]'],
            ['id' => 'directory-banner-section', 'label' => '[4. DIRECTORY]'],
        ]"
    />

    <main id="main-content" class="grow">
        {{-- Section 1: COMETS Hero Section with Embedded Media Carousel (PRD §3.1.1) --}}
        <x-landing.hero :content="$landingContent['hero'] ?? null" :moments="$landingContent['moments'] ?? null" />

        {{-- Section 2: Bee-ner Mascot & Jargon Identity Section (PRD §3.1.2) --}}
        <x-landing.decoding-ascent :content="$landingContent['decoding'] ?? null" />

        {{-- Section 3: Member Angkatan & Stats Matrix Banner (PRD §3.1.3) --}}
        <x-landing.directory-banner :content="$landingContent['directory_banner'] ?? null" />
    </main>
</x-layouts.app>
