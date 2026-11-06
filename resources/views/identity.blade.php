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
        @livewireStyles
        <style>
        </style>
    </head>
    <body class="bg-surface-canvas text-primary-900 antialiased min-h-screen flex flex-col justify-between">
        <x-navbar activePage="identity" />

        <main class="flex-grow">
            {{-- Section 4: Logo Symbolism Split Carousel (50/50 & Layer Breakdown) (Issue #25) --}}
            {{-- <x-identity.logo-carousel /> --}}

            {{-- Section 5: Mascot Anatomy Split Carousel (50/50 & Attribute Breakdown) (Issue #26) --}}
            <x-identity.mascot-carousel />
        </main>

        <x-footer activePage="identity" />
        @livewireScripts
    </body>
</html>
