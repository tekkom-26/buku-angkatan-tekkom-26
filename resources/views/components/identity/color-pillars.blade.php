@props([
    'content' => null,
    'pillars' => null,
])

@php
    $c = $content ?? [];

    $cometsPillars = [
        [
            'index' => '01',
            'name' => 'Charcoal',
            'role' => 'Kebersamaan // 60% Canvas & Colony Base',
            'hexLabel' => '#252525',
            'tokenName' => 'surface-card',
            'swatchClass' => 'bg-surface-card text-text-primary border border-white/15',
            'cardBorderClass' => 'border-white/15 hover:border-brand-gold/40',
            'philosophy' => 'Melambangkan nilai Kebersamaan: fondasi koloni yang teduh, kokoh, dan menyatukan seluruh latar belakang anggota angkatan Teknik Komputer 2026 dalam satu ruang tumbuh bersama.',
        ],
        [
            'index' => '02',
            'name' => 'Tech Gold',
            'role' => 'Potensi Diri // 30% Highlight & Circuit Glow',
            'hexLabel' => '#D4AF37',
            'tokenName' => 'brand-gold',
            'swatchClass' => 'bg-brand-gold text-canvas-base border border-brand-gold-glow',
            'cardBorderClass' => 'border-brand-gold/40 hover:border-brand-gold glow-gold-subtle',
            'philosophy' => 'Melambangkan Potensi Diri dan kejayaan rekayasa: kilau konduktor emas pada sirkuit prosesor dan madu sarang lebah yang memancarkan inovasi, prestasi, dan daya cipta.',
        ],
        [
            'index' => '03',
            'name' => 'Blood Red',
            'role' => 'Ketangguhan // 10% Primary Action & Ember',
            'hexLabel' => '#690F1D',
            'tokenName' => 'brand-red',
            'swatchClass' => 'bg-brand-red text-text-primary border border-brand-red-hover',
            'cardBorderClass' => 'border-brand-red-hover/60 hover:border-brand-red-hover glow-red-subtle',
            'philosophy' => 'Melambangkan nilai Ketangguhan: bara keberanian, tekad baja yang pantang padam saat melewati masa-masa sulit, serta energi penggerak utama koloni COMETS 2026.',
        ],
    ];

    $pillars = (! empty($c['pillars']) && count($c['pillars']) === 3) ? $c['pillars'] : $cometsPillars;

    $chantStanzas = $c['chant_stanzas'] ?? [
        [
            'number' => '01',
            'tag' => '[BAIT_01 // KEBERSAMAAN & SUKA DUKA]',
            'title' => 'Bait 1 — Cerita Kebersamaan',
            'accent' => 'gold',
            'lines' => [
                'Ada cerita tentang sebuah canda dan tawa',
                'Suka dan duka kami bersama',
                'Bangun ketangguhan, kerja sama dan menghargai',
                'Jatuh lagi, bangkit lagi!',
                'Tertawa yang keras kawanku sayang',
                'Kumpul lagi, asik lagi!',
            ],
        ],
        [
            'number' => '02',
            'tag' => '[BAIT_02 // SOLIDARITAS COMETS]',
            'title' => 'Bait 2 — Solidaritas & Kolaborasi',
            'accent' => 'red',
            'lines' => [
                'Inilah kami Comets',
                'Solid, solid bersama,',
                'Kalau solid pasti bisa!',
                'Hey Comets!',
                'Saling menghargai!',
                'Bekerja sama, kami selalu terlibat!',
            ],
        ],
        [
            'number' => '03',
            'tag' => '[BAIT_03 // KETANGGUHAN & PEMBUKTIAN]',
            'title' => 'Bait 3 — Bangkit & Buktikan',
            'accent' => 'red',
            'lines' => [
                'Inilah kami Comets!',
                'Tetap bertahan, ha ha ha!',
                'Bangkit berjuang!',
                'Akan kami buktikan!',
            ],
        ],
    ];

    $fullChantText = collect($chantStanzas)
        ->map(fn (array $stanza): string => implode("\n", $stanza['lines']))
        ->implode("\n\n") . "\n\nSOLID LIKE A BEE, BE LIKE WE WANT TO BE, STRONG AND FREE!";
@endphp

{{-- ==========================================================================
     IDENTITY — SECTION 5: COLOR PILLARS (3 WARNA RESMI) & COLONY CHANTS (3 BAIT)
     Mengacu pada .revamp-ui/PRD.md §3.2.5, DESIGN_TASTE.md & DESIGN_TOKENS.md
     ========================================================================== --}}
<section
    id="identity-color-pillars-section"
    aria-labelledby="color-pillars-heading"
    {{ $attributes->merge(['class' => 'relative w-full bg-ambient-matrix text-text-primary py-12 sm:py-16 lg:py-20 border-t border-white/10 overflow-hidden']) }}
>
    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="mb-8 sm:mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-white/10 pb-6">
            <div>
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-brand-gold mb-2">
                    &gt; // 02_COLOR_SYSTEM // 60-30-10_RULE
                </p>
                <h2
                    id="color-pillars-heading"
                    class="font-display text-2xl sm:text-4xl font-bold text-text-primary tracking-tight leading-tight"
                >
                    Tiga Pilar Warna Resmi &amp; Yel-Yel {{ $cohortName ?? 'COMETS 2026' }}
                </h2>
            </div>
            <span class="px-3 py-1 rounded-md bg-surface-card border border-brand-gold/30 font-mono text-xs uppercase tracking-wider text-brand-gold">
                PALETTE // 03_SEMANTIC_TOKENS
            </span>
        </div>

        {{-- 1. 3-Card Horizontal Color Swatch Grid (Charcoal, Tech Gold, Blood Red) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch mb-10 sm:mb-12">

            @foreach ($pillars as $pillar)
                <article class="rounded-xl border {{ $pillar['cardBorderClass'] ?? 'border-white/10' }} bg-surface-card/90 p-5 sm:p-6 flex flex-col justify-between gap-5 transition-colors">
                    <div>
                        {{-- Physical Color Swatch Block --}}
                        <div class="h-36 w-full rounded-lg p-4 flex flex-col justify-between {{ $pillar['swatchClass'] }} mb-5">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-xs font-bold uppercase tracking-wider">
                                    PILLAR_{{ $pillar['index'] }}
                                </span>
                                <span class="font-mono text-[10px] uppercase tracking-wider opacity-85">
                                    --color-{{ $pillar['tokenName'] }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-mono text-sm font-bold uppercase tracking-wider">
                                    {{ strtoupper($pillar['hexLabel']) }}
                                </span>
                                <span class="font-mono text-[10px] uppercase tracking-wider">
                                    [COMETS_26]
                                </span>
                            </div>
                        </div>

                        {{-- Pillar Name & HEX Code Chip Row --}}
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <h3 class="font-display text-xl font-bold text-text-primary">
                                {{ $pillar['name'] }}
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md bg-canvas-base border border-white/15 font-mono text-xs font-semibold text-brand-gold">
                                {{ $pillar['hexLabel'] }}
                            </span>
                        </div>

                        {{-- Role Subtitle --}}
                        <p class="font-mono text-xs font-semibold uppercase tracking-wider text-brand-gold mb-3">
                            {{ $pillar['role'] }}
                        </p>

                        {{-- Philosophical Meaning Text --}}
                        <p class="font-body text-sm leading-relaxed text-text-secondary">
                            {{ $pillar['philosophy'] }}
                        </p>
                    </div>

                    {{-- Card Bottom Token Metadata --}}
                    <div class="pt-3 border-t border-white/10 flex items-center justify-between gap-2 font-mono text-[10px] uppercase tracking-wider text-text-muted">
                        <span>TOKEN: {{ strtoupper($pillar['tokenName']) }}</span>
                        <span class="text-brand-gold">[VERIFIED_WCAG_AA]</span>
                    </div>
                </article>
            @endforeach

        </div>

        {{-- 2. Audio Card & Lirik Lengkap 3 Bait Yel-Yel Angkatan COMETS (PRD §3.2.5) --}}
        <div
            x-data="{
                activeStanza: '01',
                copiedChant: false,
                copyChantText() {
                    navigator.clipboard.writeText(@js($fullChantText));
                    this.copiedChant = true;
                    setTimeout(() => this.copiedChant = false, 2000);
                }
            }"
            class="rounded-xl border border-brand-red-hover/60 bg-surface-card/90 p-6 sm:p-8 lg:p-10 relative overflow-hidden glow-red-subtle"
        >
            {{-- Top Dual Circuit Bar --}}
            <div class="absolute inset-x-0 top-0 h-1 flex" aria-hidden="true">
                <div class="w-1/3 bg-brand-red"></div>
                <div class="w-2/3 bg-brand-gold"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">

                {{-- ── KOLOM KIRI (lg:col-span-5): Audio Console & Jargon Callout ── --}}
                <div class="lg:col-span-5 space-y-5">
                    <div class="flex items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-brand-red border border-brand-red-hover font-mono text-xs font-bold uppercase tracking-wider text-text-primary">
                            <span class="size-2 rounded-full bg-brand-gold animate-pulse" aria-hidden="true"></span>
                            <span>// COLONY_CHANT // AUDIO_CARD</span>
                        </span>
                        <span class="font-mono text-[11px] uppercase tracking-wider text-brand-gold">
                            03_STANZAS
                        </span>
                    </div>

                    <div>
                        <h3 class="font-display text-2xl sm:text-3xl font-bold text-text-primary tracking-tight">
                            Lagu &amp; Yel-Yel Resmi COMETS 2026
                        </h3>
                        <p class="mt-2 font-body text-sm text-text-secondary leading-relaxed">
                            Tiga bait nyanyian kebersamaan dan ketangguhan yang merangkum suka duka, solidaritas, serta semangat juang mahasiswa Teknik Komputer 2026.
                        </p>
                    </div>

                    {{-- Official Jargon Callout Card --}}
                    <div class="border-l-4 border-l-brand-gold bg-brand-gold/10 border border-brand-gold/30 rounded-r-xl p-4">
                        <span class="font-mono text-[10px] uppercase tracking-wider text-brand-gold block mb-1">
                            // JARGON_UTAMA // OUTRO_CRY
                        </span>
                        <p class="font-display text-base sm:text-lg font-bold text-text-primary tracking-tight leading-snug">
                            "COMETS! SOLID LIKE A BEE, BE LIKE WE WANT TO BE, COMETS! STRONG AND FREE!"
                        </p>
                    </div>

                    {{-- Simulated Audio Waveform Console Card --}}
                    <div class="rounded-xl border border-brand-gold/30 bg-canvas-base/90 p-4 space-y-3 glow-gold-subtle">
                        <div class="flex items-center justify-between font-mono text-[11px] text-brand-gold">
                            <span>&gt; TRACK: COMETS_ANTHEM_2026.WAV</span>
                            <span>[03_BAIT // READY]</span>
                        </div>

                        {{-- Geometric Waveform Bars --}}
                        <div class="flex items-end justify-between gap-1 h-10 px-2 py-1 bg-surface-card/70 rounded-lg border border-white/10" aria-hidden="true">
                            <span class="w-1.5 h-3 bg-brand-gold/60 rounded-xs"></span>
                            <span class="w-1.5 h-6 bg-brand-gold rounded-xs"></span>
                            <span class="w-1.5 h-8 bg-brand-red-hover rounded-xs"></span>
                            <span class="w-1.5 h-5 bg-brand-gold rounded-xs"></span>
                            <span class="w-1.5 h-7 bg-brand-gold-glow rounded-xs"></span>
                            <span class="w-1.5 h-4 bg-brand-red rounded-xs"></span>
                            <span class="w-1.5 h-8 bg-brand-gold rounded-xs"></span>
                            <span class="w-1.5 h-6 bg-brand-red-hover rounded-xs"></span>
                            <span class="w-1.5 h-3 bg-brand-gold/60 rounded-xs"></span>
                            <span class="w-1.5 h-7 bg-brand-gold rounded-xs"></span>
                            <span class="w-1.5 h-5 bg-brand-red-hover rounded-xs"></span>
                            <span class="w-1.5 h-8 bg-brand-gold-glow rounded-xs"></span>
                            <span class="w-1.5 h-4 bg-brand-gold/70 rounded-xs"></span>
                            <span class="w-1.5 h-6 bg-brand-red rounded-xs"></span>
                            <span class="w-1.5 h-3 bg-brand-gold/50 rounded-xs"></span>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <span class="font-mono text-[10px] uppercase tracking-wider text-text-muted">
                                BINARY_PULSE // 128_BPM
                            </span>
                            <button
                                type="button"
                                @click="copyChantText()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover font-mono text-[11px] font-semibold text-text-primary transition-colors cursor-pointer"
                            >
                                <span x-show="!copiedChant">Salin Lirik Lengkap</span>
                                <span x-cloak x-show="copiedChant" class="text-brand-gold">Lirik Tersalin!</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ── KOLOM KANAN (lg:col-span-7): Lirik Lengkap 3 Bait Yel-Yel Angkatan ── --}}
                <div class="lg:col-span-7 rounded-xl border border-white/10 bg-canvas-base/85 p-5 sm:p-6 space-y-5">

                    {{-- Header & Interactive Stanza Filter Tabs --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/10 pb-4">
                        <span class="font-mono text-xs uppercase tracking-wider text-brand-gold font-bold">
                            &gt; CHANT_TRANSCRIPT // 03_BAIT_LAGU
                        </span>

                        <div class="flex flex-wrap items-center gap-1.5" role="tablist" aria-label="Filter bait yel-yel">
                            <button
                                type="button"
                                role="tab"
                                @click="activeStanza = 'all'"
                                :aria-selected="activeStanza === 'all' ? 'true' : 'false'"
                                :class="activeStanza === 'all'
                                    ? 'bg-brand-gold/20 border-brand-gold text-brand-gold font-semibold'
                                    : 'bg-surface-card border-white/10 text-text-secondary hover:text-text-primary'"
                                class="px-2.5 py-1 rounded-md border font-mono text-[10px] uppercase tracking-wider transition-colors cursor-pointer"
                            >
                                Semua (3 Bait)
                            </button>
                            @foreach ($chantStanzas as $stanza)
                                <button
                                    type="button"
                                    role="tab"
                                    @click="activeStanza = '{{ $stanza['number'] }}'"
                                    :aria-selected="activeStanza === '{{ $stanza['number'] }}' ? 'true' : 'false'"
                                    :class="activeStanza === '{{ $stanza['number'] }}'
                                        ? 'bg-brand-red border-brand-red-hover text-text-primary font-semibold'
                                        : 'bg-surface-card border-white/10 text-text-secondary hover:text-text-primary'"
                                    class="px-2.5 py-1 rounded-md border font-mono text-[10px] uppercase tracking-wider transition-colors cursor-pointer"
                                >
                                    Bait {{ $stanza['number'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- 3 Stanza Cards --}}
                    <div class="space-y-4">
                        @foreach ($chantStanzas as $stanza)
                            <article
                                x-show="activeStanza === 'all' || activeStanza === '{{ $stanza['number'] }}'"
                                x-transition.opacity.duration.200ms
                                class="p-4 sm:p-5 rounded-xl border {{ ($stanza['accent'] ?? 'gold') === 'red' ? 'border-l-4 border-l-brand-red border-brand-red-hover/50 bg-brand-red/15' : 'border-l-4 border-l-brand-gold border-brand-gold/30 bg-brand-gold/10' }}"
                            >
                                <div class="flex items-center justify-between gap-2 mb-3 pb-2 border-b border-white/10">
                                    <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-brand-gold">
                                        {{ $stanza['tag'] }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-canvas-base/80 border border-white/10 font-mono text-[10px] uppercase tracking-wider text-text-secondary">
                                        STANZA_{{ $stanza['number'] }}
                                    </span>
                                </div>

                                <div class="space-y-1.5 font-display text-base sm:text-lg font-medium text-text-primary leading-relaxed">
                                    @foreach ($stanza['lines'] as $line)
                                        <p class="{{ str_ends_with($line, '!') ? 'font-bold text-brand-gold' : 'text-text-primary' }}">
                                            {{ $line }}
                                        </p>
                                    @endforeach
                                </div>
                            </article>
                        @endforeach
                    </div>

                </div>

            </div>
        </div>

    </div>
</section>
