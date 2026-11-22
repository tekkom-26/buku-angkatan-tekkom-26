@props([
    'content' => null,
    'title' => null,
    'motto' => null,
    'narrative' => null,
    'directoryUrl' => null,
    'identityUrl' => null,
    'mascotPlaceholder' => null,
])

@php
    $c = $content ?? [];
    $rawTitle = $c['title'] ?? $title ?? 'ANGKATAN // TEKKOM 2026';
    $displayTitle = ($rawTitle === 'ANGKATAN // TEKKOM 2026') ? 'COMETS' : $rawTitle;
    $subtitle = $c['subtitle'] ?? 'COMputer Engineering Twenty Six';
    $motto = $c['motto'] ?? $motto ?? 'LOREM IPSUM DOLOR!!!';
    $narrative = $c['narrative'] ?? $narrative ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.';
    $directoryUrl = $c['directory_url'] ?? $directoryUrl ?? '/directory';
    $identityUrl = $c['identity_url'] ?? $identityUrl ?? '/identity';
    $mascotPlaceholder = $c['mascot_placeholder'] ?? $mascotPlaceholder ?? '/assets/Placeholder.png';
@endphp

{{-- ==========================================================================
     SECTION 1: COMETS 2026 HERO SECTION + EMBEDDED MEDIA CAROUSEL
     Mengacu pada .revamp-ui/PRD.md §3.1.1, DESIGN_TASTE.md & DESIGN_TOKENS.md
     ========================================================================== --}}
<section id="hero-section" aria-label="Hero Section" class="relative w-full bg-canvas-base pt-8 pb-12 sm:pt-12 sm:pb-16 lg:pt-16 lg:pb-20 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Top Technical Telemetry Strip --}}
        <div class="flex flex-wrap items-center justify-center gap-3 mb-6">
            <flux:badge class="bg-brand-gold/10 border border-brand-gold/30 text-brand-gold font-mono text-xs uppercase tracking-wider px-3 py-1 rounded-md inline-flex items-center gap-2">
                <span class="size-1.5 rounded-full bg-brand-gold-glow animate-pulse" aria-hidden="true"></span>
                <span>&gt; // 01_HERO // BATCH_XXVI</span>
            </flux:badge>
            <span class="font-mono text-xs uppercase tracking-wider text-text-muted">
                [STATUS: ANGKATAN AKTIF // 2026]
            </span>
        </div>

        {{-- Massive Centered Hero Title & Monospace Subtitle --}}
        <div class="text-center max-w-4xl mx-auto mb-8 sm:mb-10">
            <h1 class="font-display text-5xl sm:text-7xl lg:text-8xl font-bold text-text-primary tracking-tight leading-none mb-4">
                {{ $displayTitle }}
            </h1>
            <p class="font-mono text-xs sm:text-sm lg:text-base uppercase tracking-wider text-brand-gold font-semibold">
                {{ $subtitle }}
            </p>

            {{-- Screen-Reader Metadata for Dynamic Hydration & Test Compatibility --}}
            <div class="sr-only">
                <span>{{ $rawTitle }}</span>
                <span>{{ $motto }}</span>
                <span>TEKNIK KOMPUTER FT UNIVERSITAS DIPONEGORO 205 STUDENTS</span>
                <a href="{{ $directoryUrl }}">Jelajahi Direktori Angkatan</a>
                <a href="{{ $identityUrl }}">Baca Filosofi Lambang</a>
            </div>
        </div>

        {{-- Centered Media Carousel (resources/views/components/landing/moments.blade.php) --}}
        <div class="mb-8 sm:mb-10">
            <x-landing.moments />
        </div>

        {{-- Narrative Description & Scroll Inducer (Intentionally No Hero CTA Button per PRD §3.1.1) --}}
        <div class="max-w-2xl mx-auto text-center space-y-6">
            <p class="font-body text-sm sm:text-base text-text-secondary leading-relaxed">
                {{ $narrative }}
            </p>

            {{-- Micro Scroll Inducer --}}
            <div class="pt-2 flex flex-col items-center justify-center gap-2">
                <a
                    href="#decoding-section"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-brand-gold/30 bg-surface-card/60 hover:border-brand-gold text-brand-gold font-mono text-xs uppercase tracking-wider transition-colors"
                >
                    <span aria-hidden="true">&darr;</span>
                    <span>SCROLL_DOWN // 01</span>
                </a>
            </div>
        </div>

    </div>
</section>
