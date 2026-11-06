<x-layouts.app activePage="home" title="Ascent — Teknik Komputer 2026">
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
        <x-landing.hero />

        {{-- Section 2: Decoding ASCENT 2-Column Editorial Split (Issue #19) --}}
        <x-landing.decoding-ascent />

        {{-- Section 3: Moments & Highlights Carousel (Issue #20) --}}
        <x-landing.moments />

        {{-- Section 4: Directory Quick Discovery Banner Plate (Issue #21) --}}
        <x-landing.directory-banner />
    </main>
</x-layouts.app>
