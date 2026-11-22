@props([
    'title' => null,
    'activePage' => 'home', // 'home' | 'identity' | 'directory' | 'student' | 'unlock' | 'gallery' | 'contact'
    'description' => null,
    'withNavbar' => true,
    'withFooter' => true,
])

@php
    $displayCohort = $cohortName ?? 'TEKNIK KOMPUTER 2026';
    $resolvedTitle = $title ?? ($displayCohort . ' — Buku Angkatan');
    $resolvedDescription = $description ?? ('Website Resmi Angkatan ' . $displayCohort . ' (COMETS) — Universitas Diponegoro.');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $resolvedDescription }}">

        <title>{{ $resolvedTitle }}</title>

        {{-- Google Fonts: Space Grotesk (Display), Inter (Body), JetBrains Mono (Technical Meta) --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        @livewireStyles
        @stack('styles')
    </head>
    <body class="relative min-h-screen overflow-x-hidden bg-canvas-base text-text-primary font-body antialiased selection:bg-brand-gold selection:text-canvas-base flex flex-col justify-between">

        {{-- Global Atmospheric Backdrop: Hexagonal Honeycomb, Circuit Grid & 26 GPU Colony Particles --}}
        <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden" aria-hidden="true">
            <div class="absolute inset-0 bg-hex-pattern opacity-60"></div>
            <div class="absolute inset-0 bg-colony-grid"></div>
            <x-background-particles />
        </div>

        {{-- Skip to Main Content Accessibility Link --}}
        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[110] focus:rounded-lg focus:bg-surface-card focus:px-4 focus:py-3 focus:font-display focus:font-semibold focus:text-brand-gold focus:shadow-lg focus:border focus:border-brand-gold/40"
        >
            Lewati ke konten utama
        </a>

        {{-- Global Shell: Floating Pill Navbar --}}
        @if ($withNavbar)
            <x-navbar :activePage="$activePage" />
        @endif

        {{-- Main Page Slot Content --}}
        <div class="relative z-10 grow flex flex-col">
            {{ $slot }}
        </div>

        {{-- Global Shell: Footer --}}
        @if ($withFooter)
            <div class="relative z-10">
                <x-footer :activePage="$activePage" />
            </div>
        @endif

        {{-- Livewire Runtime & Bundled Alpine.js Engine --}}
        @livewireScripts
        @stack('scripts')
    </body>
</html>
