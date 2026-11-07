@props([
    'content' => null,
    'title' => null,
    'subhead' => null,
    'bannerPlaceholder' => null,
])

@php
    $c = $content ?? [];
    $title = $c['title'] ?? $title ?? 'Identitas, Filosofi & Nilai Dasar';
    $subhead = $c['subhead'] ?? $subhead ?? 'Dokumentasi komprehensif mengenai arsitektur visual, arah gerak strategis, empat pilar warna, serta pembedahan simbolisme lambang dan maskot resmi angkatan Teknik Komputer 2026.';
    $bannerPlaceholder = $c['banner_placeholder'] ?? $bannerPlaceholder ?? '/assets/Placeholder.png';
@endphp

{{-- ==========================================================================
     SCREEN 5 — SECTION 1: EDITORIAL HEADER & 21:9 BATCH HERO BANNER
     Mengacu pada GitHub Issue #22 & #16, DESIGN.md, dan DOCS_COLOR_CONVENTION.md
     Figma Reference: Node 1:2138 (Header Frame) & Node 1:2151 (21:9 Banner 1216x304)
     ========================================================================== --}}
<section
    id="identity-header-section"
    aria-labelledby="identity-page-heading"
    {{ $attributes->merge(['class' => 'w-full bg-surface-canvas text-primary-900 pt-10 pb-8 sm:pt-14 sm:pb-12']) }}
>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- 1. Editorial Metadata Index & Display Heading Block (Figma Node 1:2138) --}}
        <header class="mb-8 sm:mb-10">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-2">
                    <span class="inline-block size-2 rounded-full bg-secondary-500" aria-hidden="true"></span>
                    <span class="font-mono text-xs font-bold uppercase tracking-[0.05em] text-secondary-700">
                        // 05_IDENTITY — FILOSOFI & SIMBOLISME ASCENT 2026
                    </span>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-lg bg-surface-card border border-surface-border font-mono text-[11px] font-semibold uppercase tracking-[0.05em] text-primary-700">
                    BATCH_XXVI // SPECIFICATION
                </span>
            </div>

            {{-- H1 Display (38px - 42px, Bold Deep Plum, Tracking -0.025em) --}}
            <h1
                id="identity-page-heading"
                class="text-3xl sm:text-4xl lg:text-[42px] font-bold text-primary-900 tracking-[-0.025em] leading-[1.12] max-w-3xl"
            >
                {{ $title }}
            </h1>

            {{-- Subhead Description (15px, line-height 1.6, Deep Plum / 85%) --}}
            <p class="mt-3.5 text-[15px] leading-[1.6] text-primary-900/85 max-w-3xl">
                {{ $subhead }}
            </p>
        </header>

        {{-- 2. Cinematic 21:9 Batch Hero Banner Container (Figma Node 1:2151, 1216x304px) --}}
        <div class="relative w-full aspect-[21/9] rounded-2xl border border-surface-border bg-surface-card p-3 sm:p-5 flex flex-col justify-between overflow-hidden shadow-xs">

            {{-- Diagonal Wireframe Crosslines Background --}}
            <svg class="absolute inset-0 h-full w-full stroke-primary-900/10 pointer-events-none" preserveAspectRatio="none" viewBox="0 0 100 100" fill="none" aria-hidden="true">
                <line x1="0" y1="0" x2="100" y2="100" vector-effect="non-scaling-stroke" stroke-width="1" />
                <line x1="100" y1="0" x2="0" y2="100" vector-effect="non-scaling-stroke" stroke-width="1" />
            </svg>

            {{-- Top Telemetry Strip inside 21:9 Frame --}}
            <div class="relative z-10 flex flex-wrap items-center justify-between gap-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-white/90 border border-surface-border font-mono text-[10px] font-bold uppercase tracking-[0.05em] text-primary-900">
                    [X] [IMAGE: BATCH_HERO_BANNER_21:9 — FOTO_KOLABORATIF_ANGKATAN]
                </span>
                <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-md bg-highlight-500 text-highlight-900 font-mono text-[10px] font-bold uppercase tracking-[0.05em]">
                    1216 × 304 PX // FT UNDIP
                </span>
            </div>

            {{-- Center Placeholder Image Slot --}}
            <div class="relative z-10 flex-1 w-full flex items-center justify-center overflow-hidden my-1">
                {{--
                    TODO: Ganti $bannerPlaceholder ('/assets/Placeholder.png') dengan path foto kolaboratif angkatan 21:9 saat aset final tersedia:
                    contoh: '/assets/identity/batch-hero-banner.jpg'
                --}}
                <img
                    src="{{ asset($bannerPlaceholder) }}"
                    alt="[X] [IMAGE: BATCH_HERO_BANNER_21:9 — FOTO_KOLABORATIF_ANGKATAN]"
                    class="max-h-full max-w-full object-contain select-none rounded-lg"
                    loading="lazy"
                />
            </div>

            {{-- Bottom Corner Telemetry Tag --}}
            <div class="relative z-10 flex flex-wrap items-center justify-between gap-2">
                <span class="font-mono text-[10px] uppercase tracking-[0.05em] text-primary-700 bg-white/80 px-2.5 py-0.5 rounded border border-surface-border">
                    [ARCHIVE_REF: ID_BANNER_2026] | ASPECT_RATIO: 21:9
                </span>
                <span class="font-mono text-[10px] uppercase tracking-[0.05em] text-secondary-700 font-bold bg-white/80 px-2.5 py-0.5 rounded border border-surface-border">
                    // NODE_1:2151
                </span>
            </div>

        </div>

    </div>
</section>
