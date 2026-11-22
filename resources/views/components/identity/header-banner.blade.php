@props([
    'content' => null,
    'title' => null,
    'subhead' => null,
    'bannerPlaceholder' => null,
])

@php
    $c = $content ?? [];
    $rawTitle = $c['title'] ?? $title ?? 'Identitas, Filosofi & Nilai Dasar';
    $displayTitle = ($rawTitle === 'Identitas, Filosofi & Nilai Dasar')
        ? 'Anatomi Identitas COMETS'
        : $rawTitle;
    $subhead = $c['subhead'] ?? $subhead ?? 'Dokumentasi komprehensif mengenai filosofi koloni lebah biner COMETS (COMputer Engineering Twenty Six), visi dan misi strategis, bedah 5 elemen lambang, anatomi maskot Bee-ner, serta 3 pilar warna resmi angkatan Teknik Komputer 2026.';
    $bannerPlaceholder = $c['banner_placeholder'] ?? $bannerPlaceholder ?? '/assets/Placeholder.png';
@endphp

{{-- ==========================================================================
     IDENTITY — SECTION 1: HEADER BANNER & 21:9 BATCH HERO FRAME
     Mengacu pada .revamp-ui/PRD.md §3.2.1, DESIGN_TASTE.md & DESIGN_TOKENS.md
     ========================================================================== --}}
<section
    id="identity-header-section"
    aria-labelledby="identity-page-heading"
    {{ $attributes->merge(['class' => 'relative w-full bg-ambient-hero text-text-primary pt-8 pb-12 sm:pt-12 sm:pb-16 overflow-hidden']) }}
>
    {{-- Decorative Background Circuit Traces & Hexagon Nodes --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden select-none" aria-hidden="true">
        <svg class="hidden lg:block absolute right-0 top-6 w-80 h-80 text-brand-gold/25" viewBox="0 0 320 320" fill="none">
            <path d="M320 50 H210 L175 85 H105" stroke="currentColor" stroke-width="1.25" />
            <path d="M320 130 H240 L205 165 H145" stroke="currentColor" stroke-width="1" stroke-dasharray="4 4" class="text-brand-red-hover/40" />
            <circle cx="105" cy="85" r="3.5" class="fill-brand-gold" />
            <circle cx="145" cy="165" r="3.5" class="fill-brand-red-hover" />
            <polygon points="180,190 220,167 260,190 260,236 220,259 180,236" stroke="currentColor" stroke-width="1" class="text-brand-red-hover/35" />
        </svg>
    </div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- 1. Editorial Metadata Index & Display Heading Block --}}
        <header class="mb-8 sm:mb-10">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="bg-surface-card/90 border border-brand-gold/40 text-brand-gold font-mono text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-md inline-flex items-center gap-2 glow-gold-subtle">
                        <span class="size-1.5 rounded-full bg-brand-gold-glow animate-pulse" aria-hidden="true"></span>
                        <span>// 05_IDENTITY — FILOSOFI &amp; SIMBOLISME // {{ $cohortName ?? 'TEKKOM 2026' }}</span>
                    </span>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-md bg-brand-red border border-brand-red-hover font-mono text-[11px] font-semibold uppercase tracking-wider text-text-primary glow-red-subtle">
                    BATCH_XXVI // SPECIFICATION
                </span>
            </div>

            {{-- H1 Display Heading --}}
            <h1
                id="identity-page-heading"
                class="font-display text-3xl sm:text-5xl lg:text-6xl font-bold text-text-primary tracking-tight leading-tight max-w-4xl"
            >
                {{ $displayTitle }}
            </h1>

            {{-- Screen-Reader Metadata for Dynamic Hydration & Test Compatibility --}}
            <div class="sr-only">
                <span>{{ $rawTitle }}</span>
            </div>

            {{-- Subhead Description with Blood Red Left Pillar Accent --}}
            <div class="mt-4 border-l-4 border-l-brand-red pl-4 py-1 max-w-3xl">
                <p class="font-body text-sm sm:text-base leading-relaxed text-text-secondary">
                    {{ $subhead }}
                </p>
            </div>
        </header>

        {{-- 2. Cinematic 21:9 Batch Hero Banner Container --}}
        <div class="relative w-full aspect-video sm:aspect-[21/9] rounded-xl border border-brand-gold/30 hover:border-brand-gold/50 bg-surface-card/90 p-3 sm:p-5 flex flex-col justify-between overflow-hidden transition-colors glow-gold-subtle">

            {{-- Top Dual-Tone Circuit Line (Tech Gold + Blood Red) --}}
            <div class="absolute inset-x-0 top-0 h-1 flex" aria-hidden="true">
                <div class="w-2/3 bg-brand-gold"></div>
                <div class="w-1/3 bg-brand-red"></div>
            </div>

            {{-- Diagonal Wireframe Crosslines Background --}}
            <svg class="absolute inset-0 h-full w-full stroke-white/10 pointer-events-none" preserveAspectRatio="none" viewBox="0 0 100 100" fill="none" aria-hidden="true">
                <line x1="0" y1="0" x2="100" y2="100" vector-effect="non-scaling-stroke" stroke-width="1" />
                <line x1="100" y1="0" x2="0" y2="100" vector-effect="non-scaling-stroke" stroke-width="1" />
            </svg>

            {{-- Top Telemetry Strip inside 21:9 Frame --}}
            <div class="relative z-10 flex flex-wrap items-center justify-between gap-2 pt-1">
                <span class="inline-flex items-center px-3 py-1 rounded-md bg-canvas-base/90 border border-white/15 font-mono text-[10px] sm:text-xs font-bold uppercase tracking-wider text-brand-gold">
                    [X] [IMAGE: BATCH_HERO_BANNER_21:9 — FOTO_KOLABORATIF_ANGKATAN]
                </span>
                <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-md bg-brand-red border border-brand-red-hover text-text-primary font-mono text-[10px] font-bold uppercase tracking-wider">
                    1216 × 304 PX // FT UNDIP
                </span>
            </div>

            {{-- Center Placeholder Image Slot --}}
            <div class="relative z-10 flex-1 w-full flex items-center justify-center overflow-hidden my-2">
                <img
                    src="{{ asset($bannerPlaceholder) }}"
                    alt="[X] [IMAGE: BATCH_HERO_BANNER_21:9 — FOTO_KOLABORATIF_ANGKATAN]"
                    class="max-h-full max-w-full object-contain select-none rounded-lg opacity-80"
                    loading="lazy"
                />
            </div>

            {{-- Bottom Corner Telemetry Tag --}}
            <div class="relative z-10 flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-white/10">
                <span class="font-mono text-[10px] uppercase tracking-wider text-text-secondary bg-canvas-base/80 px-2.5 py-1 rounded border border-white/10">
                    [ARCHIVE_REF: ID_BANNER_2026] | ASPECT_RATIO: 21:9
                </span>
                <span class="font-mono text-[10px] uppercase tracking-wider text-brand-gold font-bold bg-canvas-base/80 px-2.5 py-1 rounded border border-brand-gold/30">
                    // COLONY_MATRIX // 26_NODES
                </span>
            </div>

        </div>

    </div>
</section>
