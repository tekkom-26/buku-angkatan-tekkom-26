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
<section id="hero-section" aria-label="Hero Section" class="relative w-full bg-ambient-hero pt-8 pb-12 sm:pt-12 sm:pb-16 lg:pt-16 lg:pb-20 overflow-hidden">

    {{-- Decorative Background SVG: Left & Right Geometric Circuit Traces + Hexagon Nodes --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden select-none" aria-hidden="true">
        {{-- Left Circuit Wing & Glowing Nodes --}}
        <svg class="hidden lg:block absolute left-0 top-10 w-80 h-96 text-brand-gold/25" viewBox="0 0 320 380" fill="none">
            <path d="M0 60 H110 L150 100 H230" stroke="currentColor" stroke-width="1.25" />
            <path d="M0 140 H80 L120 180 H190" stroke="currentColor" stroke-width="1" stroke-dasharray="4 4" />
            <path d="M0 240 H60 L95 205 H155" stroke="currentColor" stroke-width="1" />
            <circle cx="230" cy="100" r="4" class="fill-brand-gold" />
            <circle cx="190" cy="180" r="3.5" class="fill-brand-red-hover" />
            <circle cx="155" cy="205" r="3" class="fill-brand-gold" />
            {{-- Giant Wireframe Hexagon Node --}}
            <polygon points="90,250 130,227 170,250 170,296 130,319 90,296" stroke="currentColor" stroke-width="1" class="text-brand-red-hover/40" />
            <polygon points="105,259 130,244 155,259 155,287 130,302 105,287" stroke="currentColor" stroke-width="0.75" class="text-brand-gold/30" />
        </svg>

        {{-- Right Circuit Wing & Glowing Nodes --}}
        <svg class="hidden lg:block absolute right-0 top-10 w-80 h-96 text-brand-red-hover/35" viewBox="0 0 320 380" fill="none">
            <path d="M320 80 H210 L170 120 H90" stroke="currentColor" stroke-width="1.25" />
            <path d="M320 165 H230 L195 200 H130" stroke="currentColor" stroke-width="1" class="text-brand-gold/25" />
            <path d="M320 250 H250 L215 215 H165" stroke="currentColor" stroke-width="1" stroke-dasharray="4 4" />
            <circle cx="90" cy="120" r="4" class="fill-brand-red-hover" />
            <circle cx="130" cy="200" r="3.5" class="fill-brand-gold" />
            <circle cx="165" cy="215" r="3" class="fill-brand-red-hover" />
            {{-- Giant Wireframe Hexagon Node --}}
            <polygon points="150,255 190,232 230,255 230,301 190,324 150,301" stroke="currentColor" stroke-width="1" class="text-brand-gold/30" />
            <polygon points="165,264 190,249 215,264 215,292 190,307 165,292" stroke="currentColor" stroke-width="0.75" class="text-brand-red-hover/40" />
        </svg>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Top Technical Telemetry Strip (Balanced Tech Gold + Blood Red Accent) --}}
        <div class="flex flex-wrap items-center justify-center gap-2.5 mb-6">
            <span class="bg-surface-card/90 border border-brand-gold/40 text-brand-gold font-mono text-xs uppercase tracking-wider px-3 py-1 rounded-md inline-flex items-center gap-2 glow-gold-subtle">
                <span class="size-1.5 rounded-full bg-brand-gold-glow animate-pulse" aria-hidden="true"></span>
                <span>&gt; // 01_HERO // BATCH_XXVI</span>
            </span>
            <span class="bg-brand-red border border-brand-red-hover text-text-primary font-mono text-xs uppercase tracking-wider px-3 py-1 rounded-md inline-flex items-center gap-2 glow-red-subtle">
                <span class="size-1.5 rounded-full bg-brand-gold" aria-hidden="true"></span>
                <span>[STATUS: ANGKATAN AKTIF // 2026]</span>
            </span>
        </div>

        {{-- Massive Centered Hero Title & Monospace Subtitle --}}
        <div class="text-center max-w-4xl mx-auto mb-8 sm:mb-10">
            <h1 class="font-display text-5xl sm:text-7xl lg:text-8xl font-bold text-text-primary tracking-tight leading-none mb-5">
                {{ $displayTitle }}
            </h1>
            <div class="inline-flex items-center gap-3 px-4 py-1.5 rounded-full border border-brand-red-hover/70 bg-brand-red/30 glow-red-subtle">
                <span class="size-2 rounded-full bg-brand-gold animate-pulse" aria-hidden="true"></span>
                <p class="font-mono text-xs sm:text-sm lg:text-base uppercase tracking-wider text-brand-gold font-semibold">
                    {{ $subtitle }}
                </p>
                <span class="size-2 rounded-full bg-brand-gold animate-pulse" aria-hidden="true"></span>
            </div>

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

            {{-- Micro Scroll Inducer with Blood Red & Tech Gold Dual Accent --}}
            <div class="pt-2 flex flex-col items-center justify-center gap-2">
                <a
                    href="#decoding-section"
                    class="inline-flex items-center gap-2.5 px-4 py-2 rounded-lg border border-brand-red-hover bg-brand-red/35 hover:bg-brand-red text-text-primary font-mono text-xs uppercase tracking-wider transition-colors glow-red-subtle"
                >
                    <span class="text-brand-gold" aria-hidden="true">&darr;</span>
                    <span>SCROLL_DOWN // 01</span>
                </a>
            </div>
        </div>

    </div>
</section>
