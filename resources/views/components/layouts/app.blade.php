@props([
    'title' => 'Ascent — Teknik Komputer 2026',
    'activePage' => 'home', // 'home' | 'identity' | 'directory' | 'student' | 'unlock' | 'gallery' | 'contact'
    'description' => 'Website Resmi Angkatan Teknik Komputer 2026 — Universitas Diponegoro.',
    'withNavbar' => true,
    'withFooter' => true,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $description }}">

        <title>{{ $title }}</title>

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        @livewireStyles
        @stack('styles')
    </head>
    <body class="min-h-screen overflow-x-hidden bg-surface-canvas text-primary-900 antialiased selection:bg-secondary-500 selection:text-primary-900 flex flex-col justify-between">

        {{-- Skip to Main Content Accessibility Link --}}
        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[110] focus:rounded-lg focus:bg-surface-card focus:px-4 focus:py-3 focus:font-semibold focus:text-primary-900 focus:shadow-lg focus:border focus:border-surface-border"
        >
            Lewati ke konten utama
        </a>

        {{-- Global Shell: Navbar --}}
        @if ($withNavbar)
            <x-navbar :activePage="$activePage" />
        @endif

        {{-- Main Page Slot Content --}}
        <div class="flex-grow flex flex-col">
            {{ $slot }}
        </div>

        {{-- Global Shell: Footer --}}
        @if ($withFooter)
            <x-footer :activePage="$activePage" />
        @endif

        {{-- Livewire 3 Runtime & Bundled Alpine.js Engine --}}
        @livewireScripts
        @stack('scripts')
    </body>
</html>
