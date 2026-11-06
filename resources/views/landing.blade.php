<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Ascent — Teknik Komputer 2026</title>
        @fonts
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        @livewireStyles
    </head>
    <body class="min-h-screen overflow-x-hidden bg-surface-canvas text-primary-900 antialiased">

        <x-navbar activePage="home" />

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

        <x-footer activePage="home" />
        @livewireScripts
    </body>
</html>
