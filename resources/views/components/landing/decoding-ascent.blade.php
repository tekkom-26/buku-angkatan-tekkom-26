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
    $jargon = $c['jargon'] ?? 'SOLID LIKE A BEE, BE LIKE WE WANT TO BE, STRONG AND FREE!';
    $movementOneTitle = $c['movement_one_title'] ?? $movementOneTitle ?? '// 01_MOVEMENT_ONE';
    $movementOneHeading = $c['movement_one_heading'] ?? $movementOneHeading ?? 'Kebersamaan // Solidaritas Koloni Lebah Biner';
    $movementOneBody = $c['movement_one_body'] ?? $movementOneBody ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Layaknya satu koloni lebah yang membangun sarang heksagonal dengan presisi tinggi, setiap mahasiswa Teknik Komputer 2026 saling terhubung dan menopang tanpa ada satu pun simpul yang tertinggal.';
    $movementTwoTitle = $c['movement_two_title'] ?? $movementTwoTitle ?? '// 02_MOVEMENT_TWO';
    $movementTwoHeading = $c['movement_two_heading'] ?? $movementTwoHeading ?? 'Ketangguhan // Daya Juang Melampaui Tantangan';
    $movementTwoBody = $c['movement_two_body'] ?? $movementTwoBody ?? 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Memadukan logika komputasi keras dengan daya tahan pantang menyerah dalam melewati masa terang maupun gelap bersama-sama.';
@endphp

{{-- ==========================================================================
     SECTION 2: BEE-NER MASCOT & JARGON IDENTITY SECTION
     Mengacu pada .revamp-ui/PRD.md §3.1.2, DESIGN_TASTE.md & DESIGN_TOKENS.md
     ========================================================================== --}}
<section id="decoding-section" aria-labelledby="decoding-heading" class="relative w-full bg-ambient-colony py-12 sm:py-16 lg:py-20 border-t border-white/10 overflow-hidden">

    {{-- Decorative Background Honeycomb Cluster Watermark --}}
    <div class="pointer-events-none absolute -right-16 bottom-6 opacity-20 select-none" aria-hidden="true">
        <svg class="w-96 h-96 text-brand-gold" viewBox="0 0 240 240" fill="none">
            <polygon points="120,20 165,45 165,95 120,120 75,95 75,45" stroke="currentColor" stroke-width="1.2" />
            <polygon points="165,95 210,120 210,170 165,195 120,170 120,120" stroke="currentColor" stroke-width="1.2" />
            <polygon points="75,95 120,120 120,170 75,195 30,170 30,120" stroke="currentColor" stroke-width="1.2" class="text-brand-red-hover" />
        </svg>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section Circuit Header --}}
        <div class="flex items-center justify-between border-b border-white/10 pb-4 mb-8 sm:mb-12 font-mono text-xs uppercase tracking-wider">
            <span class="text-brand-gold">&gt; SECTION_02 // MASCOT_AND_JARGON</span>
            <span class="px-2.5 py-0.5 rounded bg-brand-red/35 border border-brand-red-hover/70 text-text-primary">[COLONY_CORE // 26_HEXAGONS]</span>
        </div>

        {{-- 2-Column Split: Left Bee-ner Mascot Illustration / Right Jargon & Core Values --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

            {{-- ── KOLOM KIRI (lg:col-span-5): Ilustrasi Maskot Bee-ner ── --}}
            <div class="lg:col-span-5">
                <div class="bg-surface-card/90 backdrop-blur-xs border border-brand-gold/30 hover:border-brand-gold/60 rounded-xl p-6 sm:p-8 transition-colors relative overflow-hidden glow-gold-subtle">
                    {{-- Top Dual Accent Line --}}
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-brand-gold via-brand-red to-brand-gold" aria-hidden="true"></div>

                    {{-- Top Technical Tag --}}
                    <div class="flex items-center justify-between gap-2 mb-6">
                        <span class="bg-brand-gold/15 border border-brand-gold/40 text-brand-gold font-mono text-xs uppercase px-2.5 py-1 rounded-md">
                            MASCOT // BEE-NER
                        </span>
                        <span class="px-2.5 py-1 rounded-md bg-brand-red border border-brand-red-hover font-mono text-[10px] uppercase tracking-wider text-text-primary font-semibold">
                            [BINARY_BEE_01]
                        </span>
                    </div>

                    {{-- Transparent Mascot Vector Container with Subtle Hexagonal Cues --}}
                    <div class="relative aspect-square w-full max-w-[320px] mx-auto rounded-xl border border-brand-gold/30 bg-canvas-base/75 p-6 flex flex-col items-center justify-between overflow-hidden">
                        <span class="w-full text-left font-mono text-[10px] uppercase tracking-wider text-brand-gold">
                            [X: Bee-ner Mascot 2D Vector]
                        </span>

                        <div class="relative flex-1 w-full flex items-center justify-center my-2">
                            <img
                                src="{{ asset($logoPlaceholder) }}"
                                alt="[X: Bee-ner Mascot 2D Vector]"
                                class="w-full h-full object-contain select-none"
                                loading="lazy"
                            />
                        </div>

                        <div class="w-full flex items-center justify-between font-mono text-[9px] uppercase tracking-wider text-text-muted pt-2 border-t border-white/10">
                            <span class="text-brand-gold">STRIP BINER 0&amp;1</span>
                            <span>MATA HEXAGON</span>
                            <span class="text-text-primary">SAYAP SIRKUIT</span>
                        </div>
                    </div>

                    {{-- Screen-Reader Metadata for Dynamic Hydration Compatibility --}}
                    <div class="sr-only">
                        <span>{{ $wordmark }}</span>
                        <span>{{ $subLabel }}</span>
                        <span>[ASSET: LOGO_XXVI_PRIMARY_1:1]</span>
                    </div>
                </div>
            </div>

            {{-- ── KOLOM KANAN (lg:col-span-7): Jargon Angkatan, 2 Nilai Inti & CTA Identity ── --}}
            <div class="lg:col-span-7 space-y-6">

                {{-- Jargon Block with Blood Red Left Pillar Accent --}}
                <div class="border-l-4 border-l-brand-red pl-5 py-3 bg-brand-red/15 border border-brand-red-hover/40 rounded-r-xl pr-4 glow-red-subtle">
                    <p class="font-mono text-xs uppercase tracking-wider text-brand-gold mb-2">
                        &gt; JARGON_RESMI // COMETS_2026
                    </p>
                    <h2
                        id="decoding-heading"
                        class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-text-primary tracking-tight leading-tight"
                    >
                        "{{ $jargon }}"
                    </h2>
                </div>

                {{-- Two Core Values Bento Cards: Kebersamaan (Tech Gold) & Ketangguhan (Blood Red) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    {{-- Nilai 1: Kebersamaan (Tech Gold Pillar) --}}
                    <div class="bg-surface-card/90 border border-brand-gold/30 border-t-4 border-t-brand-gold hover:border-brand-gold/60 rounded-xl p-5 transition-colors space-y-2.5 glow-gold-subtle">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-xs uppercase tracking-wider text-brand-gold font-bold">
                                {{ $movementOneTitle }}
                            </span>
                            <span class="px-2 py-0.5 rounded bg-brand-gold/15 border border-brand-gold/30 font-mono text-[10px] uppercase tracking-wider text-brand-gold font-semibold">
                                VALUE_01 // GOLD
                            </span>
                        </div>
                        <h3 class="font-display text-lg font-bold text-text-primary">
                            {{ $movementOneHeading }}
                        </h3>
                        <p class="font-body text-sm text-text-secondary leading-relaxed">
                            {{ $movementOneBody }}
                        </p>
                    </div>

                    {{-- Nilai 2: Ketangguhan (Blood Red Pillar) --}}
                    <div class="bg-surface-card/90 border border-brand-red-hover/60 border-t-4 border-t-brand-red hover:border-brand-red-hover rounded-xl p-5 transition-colors space-y-2.5 glow-red-subtle">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-xs uppercase tracking-wider text-brand-gold font-bold">
                                {{ $movementTwoTitle }}
                            </span>
                            <span class="px-2 py-0.5 rounded bg-brand-red border border-brand-red-hover font-mono text-[10px] uppercase tracking-wider text-text-primary font-semibold">
                                VALUE_02 // RED
                            </span>
                        </div>
                        <h3 class="font-display text-lg font-bold text-text-primary">
                            {{ $movementTwoHeading }}
                        </h3>
                        <p class="font-body text-sm text-text-secondary leading-relaxed">
                            {{ $movementTwoBody }}
                        </p>
                    </div>
                </div>

                {{-- CTA Button: Solid Blood Red (#690F1D -> bg-brand-red) linking to route('identity') --}}
                <div class="pt-3 flex flex-wrap items-center gap-4">
                    <a
                        href="{{ route('identity') }}"
                        class="inline-flex items-center justify-center gap-2.5 px-6 py-3.5 rounded-lg bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary font-display font-semibold text-sm sm:text-base transition-colors glow-red-subtle"
                    >
                        <span>Identity -&gt;</span>
                    </a>

                    <span class="font-mono text-xs uppercase tracking-wider text-text-muted">
                        // BEDAH FILOSOFI LOGO &amp; MASKOT BEE-NER
                    </span>
                </div>

            </div>

        </div>

    </div>
</section>
