<x-layouts.app activePage="home" :title="$cohortName . ' — Buku Angkatan'">
    <x-in-page-nav
        :items="[
            ['id' => 'hero-section', 'label' => '[1. HERO]'],
            ['id' => 'decoding-section', 'label' => '[2. DECODING]'],
            ['id' => 'moments-section', 'label' => '[3. MOMENTS]'],
            ['id' => 'directory-banner-section', 'label' => '[4. DIRECTORY]'],
        ]"
    />

    <main id="main-content">
        {{-- Section 1: Hero Asymmetric Bento Grid (Issue #18) --}}
        <x-landing.hero :content="$landingContent['hero'] ?? null" />

        {{-- Section 2: Decoding 2-Column Editorial Split (Issue #19) --}}
        <x-landing.decoding-ascent :content="$landingContent['decoding'] ?? null" />

        {{-- Section 3: Moments & Highlights Carousel (Issue #20) --}}
        <x-landing.moments />

        {{-- Section 4: Directory Quick Discovery Banner Plate (Issue #21) --}}
        <x-landing.directory-banner :content="$landingContent['directory_banner'] ?? null" />
    </main>
</x-layouts.app>
