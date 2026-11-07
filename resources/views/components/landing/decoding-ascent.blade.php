@props([
    'content' => null,
    'logoPlaceholder' => null,
    'wordmark' => null,
    'subLabel' => null,
    'movementOneTitle' => null,
    'movementOneHeading' => null,
    'movementOneBody' => null,
    'movementTwoTitle' => null,
    'movementTwoHeading' => null,
    'movementTwoBody' => null,
])

@php
    $c = $content ?? [];
    $logoPlaceholder = $c['logo_placeholder'] ?? $logoPlaceholder ?? '/assets/Placeholder.png';
    $wordmark = $c['wordmark'] ?? $wordmark ?? ($cohortName ?? 'TEKKOM 2026');
    $subLabel = $c['sub_label'] ?? $subLabel ?? 'Computer Engineering Twenty Six';
    $movementOneTitle = $c['movement_one_title'] ?? $movementOneTitle ?? '// 01_MOVEMENT_ONE';
    $movementOneHeading = $c['movement_one_heading'] ?? $movementOneHeading ?? 'Pendakian Kolektif Menuju Puncak Ekselensi';
    $movementOneBody = $c['movement_one_body'] ?? $movementOneBody ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Setiap langkah ke atas adalah komitmen bersama untuk tumbuh melampaui batas kemampuan teknis maupun karakter.';
    $movementTwoTitle = $c['movement_two_title'] ?? $movementTwoTitle ?? '// 02_AS_ONE';
    $movementTwoHeading = $c['movement_two_heading'] ?? $movementTwoHeading ?? 'Satu Frekuensi dalam Spektrum Keberagaman';
    $movementTwoBody = $c['movement_two_body'] ?? $movementTwoBody ?? 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Dari berbagai latar belakang daerah, menyatu sebagai keluarga besar Teknik Komputer 2026 yang saling menguatkan.';
@endphp

{{-- ==========================================================================
     SECTION 2: DECODING (2-Column Asymmetric Editorial Split)
     Mengacu pada GitHub Issue #19 & #12, DESIGN.md, dan DOCS_COLOR_CONVENTION.md
     Figma Reference: Node 24:584 (Section Decoding), 24:612 (Logo 140x140), 24:635 (Divider)
     ========================================================================== --}}
<section id="decoding-section" aria-labelledby="decoding-heading" class="w-full bg-surface-canvas py-6 sm:py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section Blueprint Divider Top with Crosshair Ticks (Figma Node 24:635) --}}
        <div class="relative flex items-center justify-between border-t border-surface-border mb-8 sm:mb-10 pt-2 select-none" aria-hidden="true">
            <span class="font-mono text-xs text-primary-500 leading-none">+</span>
            <span class="font-mono text-[10px] uppercase tracking-[0.15em] text-primary-500">
                // SECTION_02 // DECODING_IDENTITY
            </span>
            <span class="font-mono text-xs text-primary-500 leading-none">+</span>
        </div>

        {{-- Main Editorial Split Card (Surface White, Hairline Border, Rounded-2xl) --}}
        <div class="bg-white border border-surface-border rounded-2xl p-6 sm:p-8 lg:p-10 shadow-xs relative overflow-hidden">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

                {{-- ── LEFT COLUMN (~40% / lg:col-span-5): Visual & Typographic Anchor ── --}}
                <div class="lg:col-span-5 flex flex-col justify-between gap-6 lg:border-r lg:border-surface-border lg:pr-10">

                    <div>
                        {{-- Technical Section Index Tag --}}
                        <div class="flex items-center gap-2 mb-5">
                            <span class="inline-block size-2 rounded-full bg-secondary-500" aria-hidden="true"></span>
                            <span class="font-mono text-[11px] sm:text-xs font-bold uppercase tracking-wider text-primary-700">
                                // 02_DECODING // PHILOSOPHY
                            </span>
                        </div>

                        {{-- Official Logo Anchor Box 140x140px (Figma Node 24:612) --}}
                        <div class="mb-6">
                            <div class="relative size-[140px] rounded-2xl border border-surface-border bg-surface-card p-3 flex flex-col items-center justify-between overflow-hidden shadow-2xs">
                                <span class="w-full text-left font-mono text-[8px] uppercase tracking-wider text-primary-500 leading-tight">
                                    [X] [ASSET: LOGO_XXVI_PRIMARY_1:1]
                                </span>
                                <div class="relative flex-1 w-full flex items-center justify-center overflow-hidden p-1">
                                    {{--
                                        TODO: Ganti $logoPlaceholder ('/assets/Placeholder.png') dengan path gambar PNG/SVG logo resmi saat aset final tersedia:
                                        contoh: '/assets/logo/ascent-primary.png'
                                    --}}
                                    <img
                                        src="{{ asset($logoPlaceholder) }}"
                                        alt="[ASSET: LOGO_XXVI_PRIMARY_1:1]"
                                        class="w-full h-full object-contain select-none"
                                        loading="lazy"
                                    />
                                </div>
                            </div>
                        </div>

                        {{-- Massive Display Wordmark "ASCENT" --}}
                        <h2
                            id="decoding-heading"
                            class="text-5xl sm:text-6xl font-bold text-primary-900 tracking-[-0.03em] leading-none mb-3"
                        >
                            {{ $wordmark }}
                        </h2>

                        {{-- Monospace Full Expansion Sub-label --}}
                        <p class="font-mono text-xs uppercase tracking-[0.05em] text-primary-700 font-semibold leading-relaxed">
                            {{ $subLabel }}
                        </p>
                    </div>

                    {{-- Metadata Pill Row --}}
                    <div class="flex flex-wrap items-center gap-2.5 pt-4 border-t border-surface-border">
                        <span class="inline-flex items-center px-3 py-1 rounded-lg bg-surface-card border border-surface-border font-mono text-[11px] font-bold text-primary-900 uppercase tracking-wider">
                            // COHORT_XXVI
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-highlight-500 text-highlight-900 font-mono text-[11px] font-bold uppercase tracking-wider">
                            <span class="size-1.5 rounded-full bg-highlight-900" aria-hidden="true"></span>
                            [STATUS: ACTIVE_BATCH]
                        </span>
                    </div>

                </div>

                {{-- ── RIGHT COLUMN (~60% / lg:col-span-7): Flowing Editorial Narrative ── --}}
                <div class="lg:col-span-7 flex flex-col justify-center">

                    {{-- Movement 01: THE ASCENT --}}
                    <article class="space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-xs font-bold uppercase tracking-[0.05em] text-secondary-700">
                                {{ $movementOneTitle }}
                            </span>
                            <span class="font-mono text-[10px] uppercase tracking-widest text-primary-500">
                                MOVEMENT_01
                            </span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-semibold text-primary-900 tracking-[-0.02em]">
                            {{ $movementOneHeading }}
                        </h3>
                        <p class="text-[15px] leading-[1.7] text-primary-900/85 max-w-[70ch]">
                            {{ $movementOneBody }}
                        </p>
                    </article>

                    {{-- Hairline Divider with Crosshair Intersections --}}
                    <div class="relative my-6 sm:my-8 flex items-center justify-between border-t border-surface-border" aria-hidden="true">
                        <span class="bg-white pr-2 -mt-2 font-mono text-xs text-primary-500 leading-none">+</span>
                        <span class="bg-white px-2 -mt-2 font-mono text-[10px] uppercase tracking-widest text-primary-500">
                            SYNCHRONIZED_AXIS
                        </span>
                        <span class="bg-white pl-2 -mt-2 font-mono text-xs text-primary-500 leading-none">+</span>
                    </div>

                    {{-- Movement 02: AS ONE --}}
                    <article class="space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded bg-highlight-500 text-primary-900 font-mono text-xs font-bold uppercase tracking-[0.05em]">
                                {{ $movementTwoTitle }}
                            </span>
                            <span class="font-mono text-[10px] uppercase tracking-widest text-primary-500">
                                MOVEMENT_02
                            </span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-semibold text-primary-900 tracking-[-0.02em]">
                            {{ $movementTwoHeading }}
                        </h3>
                        <p class="text-[15px] leading-[1.7] text-primary-900/85 max-w-[70ch]">
                            {{ $movementTwoBody }}
                        </p>
                    </article>

                </div>

            </div>

        </div>

    </div>
</section>

