@props([
    'content' => null,
    'pillars' => null,
])

@php
    $c = $content ?? [];
    $pillars = $c['pillars'] ?? $pillars ?? [
        [
            'index' => '01',
            'name' => 'Sky Wash',
            'role' => 'Primary Interactive',
            'hexLabel' => '#80bab2',
            'tokenName' => 'secondary-500',
            'swatchClass' => 'bg-secondary-500 text-primary-900',
            'philosophy' => 'Melambangkan kejernihan berpikir, keterbukaan kolaborasi, serta energi interaktif yang menggerakkan inovasi teknologi angkatan 2026.',
        ],
        [
            'index' => '02',
            'name' => 'Butter Cream',
            'role' => 'Accent & Alert',
            'hexLabel' => '#d4d86a',
            'tokenName' => 'highlight-500',
            'swatchClass' => 'bg-highlight-500 text-primary-900',
            'philosophy' => 'Merepresentasikan optimisme hangat, penanda fokus strategis, serta semangat muda yang adaptif dalam menghadapi dinamika tantangan.',
        ],
        [
            'index' => '03',
            'name' => 'Deep Plum',
            'role' => 'Structural Grounding',
            'hexLabel' => '#514051',
            'tokenName' => 'primary-900',
            'swatchClass' => 'bg-primary-900 text-white',
            'philosophy' => 'Menjadi jangkar otoritas struktural, kedalaman intelektual, dan keteguhan prinsip yang menjaga keutuhan fondasi keluarga besar ASCENT.',
        ],
        [
            'index' => '04',
            'name' => 'Snow White',
            'role' => 'Surface Canvas',
            'hexLabel' => '#e6e3e8',
            'tokenName' => 'surface-card',
            'swatchClass' => 'bg-surface-card text-primary-900 border border-surface-border',
            'philosophy' => 'Mewujudkan ruang kanvas yang bersih, inklusif, dan harmonis sebagai wadah tumbuhnya gagasan serta dokumentasi jejak seluruh anggota.',
        ],
    ];
@endphp

{{-- ==========================================================================
     SCREEN 5 — SECTION 3: 4 OFFICIAL COLOR PILLARS (HORIZONTAL SWATCH GRID)
     Mengacu pada GitHub Issue #24 & #16, DESIGN.md, dan DOCS_COLOR_CONVENTION.md
     Figma Reference: Node 1:2172 (Color Pillars Frame)
     ========================================================================== --}}
<section
    id="identity-color-pillars-section"
    aria-labelledby="color-pillars-heading"
    {{ $attributes->merge(['class' => 'w-full bg-surface-canvas text-primary-900 py-8 sm:py-12']) }}
>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-surface-border pb-6">
            <div>
                <p class="font-mono text-xs font-bold uppercase tracking-[0.05em] text-secondary-700 mb-2">
                    // 02_COLOR_SYSTEM
                </p>
                <h2
                    id="color-pillars-heading"
                    class="text-2xl sm:text-[26px] font-bold text-primary-900 tracking-[-0.02em] leading-tight"
                >
                    Empat Pilar Warna ASCENT
                </h2>
            </div>
            <span class="font-mono text-xs uppercase tracking-[0.05em] text-primary-500">
                PALETTE // 04_SEMANTIC_TOKENS
            </span>
        </div>

        {{-- 4-Card Horizontal Swatch Grid (Desktop 4-Col, Tablet 2x2, Mobile 1-Col) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">

            @foreach ($pillars as $pillar)
                <article class="rounded-2xl border border-surface-border bg-white p-5 flex flex-col justify-between gap-4 shadow-xs">
                    <div>
                        {{-- Physical Color Swatch Block (rounded-xl) --}}
                        <div class="h-32 w-full rounded-xl p-3.5 flex flex-col justify-between {{ $pillar['swatchClass'] }} mb-4">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-xs font-bold uppercase tracking-[0.05em] opacity-80">
                                    PILLAR_{{ $pillar['index'] }}
                                </span>
                                <span class="font-mono text-[10px] uppercase tracking-wider opacity-75">
                                    --color-{{ $pillar['tokenName'] }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-xs font-bold uppercase tracking-[0.05em]">
                                    {{ strtoupper($pillar['hexLabel']) }}
                                </span>
                            </div>
                        </div>

                        {{-- Pillar Name & HEX Code Chip Row --}}
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <h3 class="text-base font-bold text-primary-900">
                                {{ $pillar['name'] }}
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-surface-card border border-surface-border font-mono text-xs font-semibold text-primary-900">
                                {{ $pillar['hexLabel'] }}
                            </span>
                        </div>

                        {{-- Role Subtitle --}}
                        <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.04em] text-secondary-700 mb-3">
                            {{ $pillar['role'] }}
                        </p>

                        {{-- Philosophical Meaning Text (13px, line-height 1.5, Deep Plum / 80%) --}}
                        <p class="text-[13px] leading-[1.5] text-primary-900/80">
                            {{ $pillar['philosophy'] }}
                        </p>
                    </div>

                    {{-- Card Bottom Token Metadata --}}
                    <div class="pt-3 border-t border-surface-border flex items-center justify-between gap-2 font-mono text-[10px] uppercase tracking-[0.05em] text-primary-500">
                        <span>TOKEN: {{ strtoupper($pillar['tokenName']) }}</span>
                        <span>[VERIFIED]</span>
                    </div>
                </article>
            @endforeach

        </div>

    </div>
</section>
