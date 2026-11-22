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
<section id="decoding-section" aria-labelledby="decoding-heading" class="w-full bg-canvas-base py-12 sm:py-16 lg:py-20 border-t border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section Circuit Header --}}
        <div class="flex items-center justify-between border-b border-white/10 pb-4 mb-8 sm:mb-12 font-mono text-xs uppercase tracking-wider">
            <span class="text-brand-gold">&gt; SECTION_02 // MASCOT_AND_JARGON</span>
            <span class="text-text-muted">[COLONY_CORE // 26_HEXAGONS]</span>
        </div>

        {{-- 2-Column Split: Left Bee-ner Mascot Illustration / Right Jargon & Core Values --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

            {{-- ── KOLOM KIRI (lg:col-span-5): Ilustrasi Maskot Bee-ner ── --}}
            <div class="lg:col-span-5">
                <flux:card class="bg-surface-card border border-white/10 hover:border-brand-gold/40 rounded-xl p-6 sm:p-8 transition-colors relative overflow-hidden">
                    {{-- Top Technical Tag --}}
                    <div class="flex items-center justify-between gap-2 mb-6">
                        <flux:badge class="bg-brand-gold/10 border border-brand-gold/30 text-brand-gold font-mono text-xs uppercase px-2.5 py-1 rounded-md">
                            MASCOT // BEE-NER
                        </flux:badge>
                        <span class="font-mono text-[10px] uppercase tracking-wider text-text-muted">
                            [BINARY_BEE_01]
                        </span>
                    </div>

                    {{-- Transparent Mascot Vector Container with Subtle Hexagonal Cues --}}
                    <div class="relative aspect-square w-full max-w-[320px] mx-auto rounded-xl border border-brand-gold/30 bg-canvas-base/60 p-6 flex flex-col items-center justify-between overflow-hidden">
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
                            <span>STRIP BINER 0&amp;1</span>
                            <span>MATA HEXAGON</span>
                            <span>SAYAP SIRKUIT</span>
                        </div>
                    </div>

                    {{-- Screen-Reader Metadata for Dynamic Hydration Compatibility --}}
                    <div class="sr-only">
                        <span>{{ $wordmark }}</span>
                        <span>{{ $subLabel }}</span>
                        <span>[ASSET: LOGO_XXVI_PRIMARY_1:1]</span>
                    </div>
                </flux:card>
            </div>

            {{-- ── KOLOM KANAN (lg:col-span-7): Jargon Angkatan, 2 Nilai Inti & CTA Identity ── --}}
            <div class="lg:col-span-7 space-y-6">

                <div>
                    <p class="font-mono text-xs uppercase tracking-wider text-brand-gold mb-3">
                        &gt; JARGON_RESMI // COMETS_2026
                    </p>
                    <h2
                        id="decoding-heading"
                        class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-text-primary tracking-tight leading-tight"
                    >
                        "{{ $jargon }}"
                    </h2>
                </div>

                {{-- Two Core Values Bento Cards: Kebersamaan & Ketangguhan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    {{-- Nilai 1: Kebersamaan --}}
                    <flux:card class="bg-surface-card border border-white/10 hover:border-brand-gold/40 rounded-xl p-5 transition-colors space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-xs uppercase tracking-wider text-brand-gold font-bold">
                                {{ $movementOneTitle }}
                            </span>
                            <span class="font-mono text-[10px] uppercase tracking-wider text-text-muted">
                                VALUE_01
                            </span>
                        </div>
                        <h3 class="font-display text-lg font-bold text-text-primary">
                            {{ $movementOneHeading }}
                        </h3>
                        <p class="font-body text-sm text-text-secondary leading-relaxed">
                            {{ $movementOneBody }}
                        </p>
                    </flux:card>

                    {{-- Nilai 2: Ketangguhan --}}
                    <flux:card class="bg-surface-card border border-white/10 hover:border-brand-gold/40 rounded-xl p-5 transition-colors space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-mono text-xs uppercase tracking-wider text-brand-gold font-bold">
                                {{ $movementTwoTitle }}
                            </span>
                            <span class="font-mono text-[10px] uppercase tracking-wider text-text-muted">
                                VALUE_02
                            </span>
                        </div>
                        <h3 class="font-display text-lg font-bold text-text-primary">
                            {{ $movementTwoHeading }}
                        </h3>
                        <p class="font-body text-sm text-text-secondary leading-relaxed">
                            {{ $movementTwoBody }}
                        </p>
                    </flux:card>
                </div>

                {{-- CTA Button: Blood Red (#690F1D -> bg-brand-red) linking to route('identity') --}}
                <div class="pt-3 flex flex-wrap items-center gap-4">
                    <flux:button
                        href="{{ route('identity') }}"
                        class="bg-brand-red hover:bg-brand-red-hover text-text-primary font-display font-medium rounded-lg px-6 py-3 border border-white/10 transition-colors inline-flex items-center gap-2"
                    >
                        <span>Identity -&gt;</span>
                    </flux:button>

                    <span class="font-mono text-xs uppercase tracking-wider text-text-muted">
                        // BEDAH FILOSOFI LOGO &amp; MASKOT BEE-NER
                    </span>
                </div>

            </div>

        </div>

    </div>
</section>
