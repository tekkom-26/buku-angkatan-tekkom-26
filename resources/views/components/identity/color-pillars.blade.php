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
            'role' => 'Kebersamaan · 60% Kanvas Utama',
            'hexLabel' => '#252525',
            'tokenName' => 'surface-card',
            'swatchClass' => 'bg-surface-card text-text-primary border border-white/15',
            'philosophy' => 'Melambangkan nilai Kebersamaan: fondasi yang teduh, kokoh, dan menyatukan seluruh latar belakang anggota angkatan Teknik Komputer 2026 dalam satu ruang tumbuh bersama.',
        ],
        [
            'index' => '02',
            'name' => 'Tech Gold',
            'role' => 'Potensi Diri · 30% Aksen Utama',
            'hexLabel' => '#D4AF37',
            'tokenName' => 'brand-gold',
            'swatchClass' => 'bg-brand-gold text-canvas-base border border-brand-gold-glow',
            'philosophy' => 'Melambangkan Potensi Diri dan kejayaan rekayasa: kilau konduktor emas pada sirkuit prosesor dan madu sarang lebah yang memancarkan inovasi serta daya cipta.',
        ],
        [
            'index' => '03',
            'name' => 'Blood Red',
            'role' => 'Ketangguhan · 10% Aksi & Tekad',
            'hexLabel' => '#690F1D',
            'tokenName' => 'brand-red',
            'swatchClass' => 'bg-brand-red text-text-primary border border-brand-red-hover',
            'philosophy' => 'Melambangkan nilai Ketangguhan: bara keberanian, tekad baja yang pantang padam saat melewati masa-masa sulit, serta energi penggerak utama koloni COMETS 2026.',
        ],
    ];

    $pillars = (! empty($c['pillars']) && count($c['pillars']) === 3) ? $c['pillars'] : $cometsPillars;

    $chantStanzas = $c['chant_stanzas'] ?? [
        [
            'number' => '01',
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

<section
    id="identity-color-pillars-section"
    aria-labelledby="color-pillars-heading"
    {{ $attributes->merge(['class' => 'relative w-full bg-ambient-matrix text-text-primary py-14 sm:py-20 border-t border-white/10 overflow-hidden']) }}
>
    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mb-10 border-b border-white/10 pb-6">
            <p class="font-display text-xs font-semibold uppercase tracking-widest text-brand-gold mb-2">
                Sistem Warna &amp; Yel-Yel
            </p>
            <h2
                id="color-pillars-heading"
                class="font-display text-2xl sm:text-4xl font-bold text-text-primary tracking-tight leading-tight"
            >
                Tiga Pilar Warna Resmi &amp; Yel-Yel {{ $cohortName ?? 'COMETS 2026' }}
            </h2>
        </div>

        {{-- 1. 3 Color Pillars Grid (Clean Surface without Nested Token Boilerplate) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch mb-12">
            @foreach ($pillars as $pillar)
                <article class="rounded-2xl border border-white/10 bg-surface-card/75 p-5 sm:p-6 flex flex-col justify-between gap-5">
                    <div>
                        <div class="h-32 w-full rounded-xl p-4 flex flex-col justify-between {{ $pillar['swatchClass'] }} mb-5">
                            <span class="font-mono text-xs font-semibold opacity-80">
                                {{ $pillar['index'] }}
                            </span>
                            <div class="flex items-end justify-between gap-2">
                                <span class="font-mono text-sm font-bold tracking-wider">
                                    {{ strtoupper($pillar['hexLabel']) }}
                                </span>
                            </div>
                        </div>

                        <h3 class="font-display text-xl font-bold text-text-primary mb-1">
                            {{ $pillar['name'] }}
                        </h3>

                        <p class="font-display text-xs font-semibold text-brand-gold mb-3">
                            {{ str_replace('//', '·', $pillar['role']) }}
                        </p>

                        <p class="font-body text-sm leading-relaxed text-text-secondary">
                            {{ $pillar['philosophy'] }}
                        </p>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- 2. Yel-Yel & Jargon Resmi (Clean Editorial Split, Zero Fake Audio Console) --}}
        <div
            x-data="{
                activeStanza: 'all',
                copiedChant: false,
                copyChantText() {
                    navigator.clipboard.writeText(@js($fullChantText));
                    this.copiedChant = true;
                    setTimeout(() => this.copiedChant = false, 2000);
                }
            }"
            class="rounded-2xl border border-white/10 bg-surface-card/80 p-6 sm:p-8 lg:p-10"
        >
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">

                {{-- Left: Summary, Jargon & Copy CTA --}}
                <div class="lg:col-span-5 space-y-6">
                    <div>
                        <p class="font-display text-xs font-semibold uppercase tracking-wider text-brand-gold mb-2">
                            Yel-Yel Angkatan
                        </p>
                        <h3 class="font-display text-2xl sm:text-3xl font-bold text-text-primary tracking-tight">
                            Lagu &amp; Yel-Yel Resmi COMETS 2026
                        </h3>
                        <p class="mt-2.5 font-body text-sm text-text-secondary leading-relaxed">
                            Tiga bait nyanyian kebersamaan dan ketangguhan yang merangkum suka duka, solidaritas, serta semangat juang mahasiswa Teknik Komputer 2026.
                        </p>
                    </div>

                    <div class="rounded-xl border border-brand-gold/30 bg-brand-gold/10 p-4 sm:p-5">
                        <p class="font-display text-xs font-semibold uppercase tracking-wider text-brand-gold mb-1.5">
                            Jargon Utama
                        </p>
                        <p class="font-display text-base sm:text-lg font-bold text-text-primary tracking-tight leading-snug">
                            “COMETS! SOLID LIKE A BEE, BE LIKE WE WANT TO BE, COMETS! STRONG AND FREE!”
                        </p>
                    </div>

                    <div>
                        <button
                            type="button"
                            @click="copyChantText()"
                            class="inline-flex items-center justify-center gap-2 min-h-11 px-5 py-2.5 rounded-xl bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover font-display text-sm font-semibold text-text-primary transition-colors cursor-pointer"
                        >
                            <span x-show="!copiedChant">Salin Lirik Lengkap</span>
                            <span x-cloak x-show="copiedChant" class="text-brand-gold">Lirik Tersalin!</span>
                        </button>
                    </div>
                </div>

                {{-- Right: 3 Stanzas with Accessible Tabs (>= 36px touch height) --}}
                <div class="lg:col-span-7 space-y-5">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 pb-4">
                        <span class="font-display text-sm font-semibold text-text-primary">
                            Lirik 3 Bait
                        </span>

                        <div class="flex flex-wrap items-center gap-2" role="tablist" aria-label="Filter bait yel-yel">
                            <button
                                type="button"
                                role="tab"
                                @click="activeStanza = 'all'"
                                :aria-selected="activeStanza === 'all' ? 'true' : 'false'"
                                :class="activeStanza === 'all'
                                    ? 'bg-brand-gold/20 border-brand-gold text-brand-gold font-semibold'
                                    : 'bg-canvas-base/60 border-white/10 text-text-secondary hover:text-text-primary'"
                                class="min-h-9 px-3 py-1.5 rounded-lg border font-display text-xs transition-colors cursor-pointer whitespace-nowrap"
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
                                        : 'bg-canvas-base/60 border-white/10 text-text-secondary hover:text-text-primary'"
                                    class="min-h-9 px-3 py-1.5 rounded-lg border font-display text-xs transition-colors cursor-pointer whitespace-nowrap"
                                >
                                    Bait {{ $stanza['number'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach ($chantStanzas as $stanza)
                            <article
                                x-show="activeStanza === 'all' || activeStanza === '{{ $stanza['number'] }}'"
                                x-transition.opacity.duration.200ms
                                class="p-4 sm:p-5 rounded-xl border {{ ($stanza['accent'] ?? 'gold') === 'red' ? 'border-brand-red-hover/40 bg-brand-red/12' : 'border-brand-gold/25 bg-brand-gold/8' }}"
                            >
                                <h4 class="font-display text-xs font-semibold uppercase tracking-wider text-brand-gold mb-3 pb-2 border-b border-white/10">
                                    {{ $stanza['title'] ?? ('Bait ' . $stanza['number']) }}
                                </h4>

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
