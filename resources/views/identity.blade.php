<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Identitas Angkatan — Teknik Komputer 2026</title>
        @fonts
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="bg-surface-canvas text-primary-900 antialiased min-h-screen flex flex-col justify-between">
        <x-navbar activePage="identity" />

        <main class="flex-grow">
            {{-- Section 3: 4 Official Color Pillars Horizontal Swatch Grid (Issue #24) --}}
            <x-identity.color-pillars />

            {{-- Section 4: Logo Symbolism Split Carousel (50/50 & Layer Breakdown) (Issue #25) --}}
            {{-- <x-identity.logo-carousel /> --}}

            {{-- Section 5: Mascot Anatomy Split Carousel (50/50 & Attribute Breakdown) (Issue #26) --}}
            <x-identity.mascot-carousel />
        </main>

        <x-footer activePage="identity" />
    </body>
</html>
