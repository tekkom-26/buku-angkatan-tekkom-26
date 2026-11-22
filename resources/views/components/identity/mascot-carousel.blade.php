@props([
    'content' => null,
    'mascotPlaceholder' => '/assets/Placeholder.png',
])

@php
    $c = $content ?? [];
    $eyebrow = $c['eyebrow'] ?? '// 04_MASCOT_ANATOMY';
    $title = $c['title'] ?? 'Bedah Karakter';
    $subtitle = $c['subtitle'] ?? '& Anatomi Maskot Bee-ner';
    $description = $c['description'] ?? 'Setiap detail anatomi pada maskot lebah biner Bee-ner dirancang untuk memancarkan kecerdasan komputasi, potensi diri yang menyala, kekuatan memikul tanggung jawab bersama, dan kepekaan sosial koloni COMETS 2026.';

    $beenerSlides = [
        [
            'number' => '01',
            'eyebrow' => 'ATRIBUT 01 // BINARY COMPUTATION',
            'title' => 'Strip Hitam-Emas // Logika Biner 0 & 1',
            'description' => 'Belang tubuh berwarna hitam Charcoal dan Tech Gold merepresentasikan bilangan biner (0 dan 1) sebagai fondasi logika komputasi Teknik Komputer sekaligus ketangguhan melewati suka dan duka.',
            'detail' => 'Biner 0 & 1 // Logika Komputasi',
            'part' => 'STRIP HITAM-EMAS',
            'accent' => 'gold',
            'wireframeLabel' => '[X: Bee-ner Mascot — Atribut 01 Strip Hitam-Emas]',
            'image' => $mascotPlaceholder,
        ],
        [
            'number' => '02',
            'eyebrow' => 'ATRIBUT 02 // IGNITED POTENTIAL',
            'title' => 'Mata Hexagon // Potensi Diri yang Menyala',
            'description' => 'Mata berbentuk segi enam (hexagon) yang bersinar keemasan melambangkan fokus, ketajaman visi rekayasa, serta potensi setiap mahasiswa COMETS 2026 yang selalu menyala.',
            'detail' => 'Potensi Menyala // Visi Hexagon',
            'part' => 'MATA HEXAGON',
            'accent' => 'red',
            'wireframeLabel' => '[X: Bee-ner Mascot — Atribut 02 Mata Hexagon]',
            'image' => $mascotPlaceholder,
        ],
        [
            'number' => '03',
            'eyebrow' => 'ATRIBUT 03 // SHARED RESPONSIBILITY',
            'title' => 'Ring Bahu // Kuat Memikul Beban Bersama',
            'description' => 'Konstruksi ring pelindung di kedua bahu Bee-ner melambangkan kekuatan fisik maupun mental untuk saling menopang dan memikul tanggung jawab besar secara kolektif.',
            'detail' => 'Kekuatan Kolektif // Pikul Beban Bersama',
            'part' => 'RING BAHU',
            'accent' => 'gold',
            'wireframeLabel' => '[X: Bee-ner Mascot — Atribut 03 Ring Bahu]',
            'image' => $mascotPlaceholder,
        ],
        [
            'number' => '04',
            'eyebrow' => 'ATRIBUT 04 // EMPATHY & CONNECTIVITY',
            'title' => 'Antena // Peka & Peduli Terhadap Sesama',
            'description' => 'Sepasang antena pemancar di kepala Bee-ner berfungsi sebagai sensor kepekaan sosial: selalu tanggap, peduli, dan terhubung dengan sinyal kondisi rekan satu angkatan.',
            'detail' => 'Peka & Peduli // Sensor Solidaritas',
            'part' => 'ANTENA',
            'accent' => 'red',
            'wireframeLabel' => '[X: Bee-ner Mascot — Atribut 04 Antena]',
            'image' => $mascotPlaceholder,
        ],
        [
            'number' => '05',
            'eyebrow' => 'ATRIBUT 05 // WARM COLONY SPIRIT',
            'title' => 'Senyum Ramah // Inklusivitas & Keterbukaan Koloni',
            'description' => 'Ekspresi senyum hangat Bee-ner menyeimbangkan ketegasan teknologi keras dengan keramahan keluarga besar Teknik Komputer 2026 yang terbuka bagi siapa saja.',
            'detail' => 'Senyum Ramah // Koloni Inklusif',
            'part' => 'SENYUM RAMAH',
            'accent' => 'gold',
            'wireframeLabel' => '[X: Bee-ner Mascot — Atribut 05 Senyum Ramah]',
            'image' => $mascotPlaceholder,
        ],
    ];

    $slides = (! empty($c['slides']) && count($c['slides']) === 5) ? $c['slides'] : $beenerSlides;
@endphp

{{-- ==========================================================================
     IDENTITY — SECTION 4: MASCOT EXPLORER (5 ATRIBUT BEE-NER)
     Mengacu pada .revamp-ui/PRD.md §3.2.4, DESIGN_TASTE.md & DESIGN_TOKENS.md
     ========================================================================== --}}
<section
    id="identity-mascot-section"
    aria-labelledby="mascot-explorer-heading"
    {{ $attributes->merge(['class' => 'relative w-full bg-ambient-colony text-text-primary py-12 sm:py-16 lg:py-20 border-t border-white/10 overflow-hidden']) }}
    x-data="{
        activeSlide: 1,
        totalSlides: {{ count($slides) }},
        next() {
            this.activeSlide = this.activeSlide === this.totalSlides ? 1 : this.activeSlide + 1;
        },
        previous() {
            this.activeSlide = this.activeSlide === 1 ? this.totalSlides : this.activeSlide - 1;
        },
        goTo(slide) {
            this.activeSlide = slide;
        }
    }"
>
    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="mb-8 sm:mb-10 flex flex-col gap-4 border-b border-white/10 pb-6 md:flex-row md:items-end md:justify-between">
            <div class="max-w-2xl">
                <p class="mb-2 font-mono text-xs font-bold uppercase tracking-wider text-brand-gold">
                    &gt; {{ $eyebrow }} // BEE-NER_SPEC
                </p>
                <h2 id="mascot-explorer-heading" class="font-display text-2xl sm:text-4xl font-bold tracking-tight text-text-primary">
                    {{ $title }}
                    <span class="text-brand-gold">{!! $subtitle !!}</span>
                </h2>
                <p class="mt-3 max-w-xl font-body text-sm sm:text-base leading-relaxed text-text-secondary">
                    {{ $description }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-md bg-brand-red border border-brand-red-hover font-mono text-xs uppercase tracking-wider text-text-primary font-semibold glow-red-subtle">
                    MASCOT / 05 ATTRIBUTES
                </span>
            </div>
        </div>

        {{-- Quick Attribute Selector Pills (5 Interactive Tabs) --}}
        <div class="flex flex-wrap items-center gap-2 mb-6" role="tablist" aria-label="Pilih atribut maskot Bee-ner">
            @foreach ($slides as $slide)
                <button
                    type="button"
                    role="tab"
                    @click="goTo({{ $loop->iteration }})"
                    :aria-selected="activeSlide === {{ $loop->iteration }} ? 'true' : 'false'"
                    :class="activeSlide === {{ $loop->iteration }}
                        ? 'bg-brand-gold/20 border-brand-gold text-brand-gold font-semibold glow-gold-subtle'
                        : 'bg-surface-card/80 border-white/10 text-text-secondary hover:border-brand-red-hover hover:text-text-primary'"
                    class="px-3.5 py-2 rounded-lg border font-mono text-xs uppercase tracking-wider transition-all duration-200 cursor-pointer inline-flex items-center gap-2"
                >
                    <span class="text-brand-gold font-bold">{{ $slide['number'] }}.</span>
                    <span>{{ $slide['part'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- Split Carousel Container (50% Visual Left / 50% Dossier Right) --}}
        <div class="grid overflow-hidden rounded-xl border border-brand-red-hover/60 bg-surface-card/90 shadow-lg lg:grid-cols-2 glow-red-subtle">

            {{-- ── LEFT: VISUAL DISPLAY VIEWPORT (50%) ── --}}
            <div class="relative min-h-[22rem] overflow-hidden bg-canvas-base p-6 sm:min-h-[28rem] sm:p-8 border-b lg:border-b-0 lg:border-r border-white/10">
                {{-- Top Dual Circuit Line --}}
                <div class="absolute inset-x-0 top-0 h-1 flex" aria-hidden="true">
                    <div class="w-1/2 bg-brand-red"></div>
                    <div class="w-1/2 bg-brand-gold"></div>
                </div>

                {{-- Subtle Crimson & Gold Radial Backdrop inside Viewport --}}
                <div class="pointer-events-none absolute inset-0 bg-ambient-colony opacity-80" aria-hidden="true"></div>

                <div class="relative z-10 grid h-full">
                    @foreach ($slides as $slide)
                        <div
                            x-show="activeSlide === {{ $loop->iteration }}"
                            x-transition:enter="transition duration-300 ease-out"
                            x-transition:enter-start="translate-x-3 opacity-0"
                            x-transition:enter-end="translate-x-0 opacity-100"
                            x-transition:leave="transition duration-200 ease-in"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="col-start-1 row-start-1 flex h-full min-h-[20rem] flex-col justify-between sm:min-h-[25rem]"
                        >
                            {{-- Top Metadata Row --}}
                            <div class="flex items-start justify-between">
                                <span class="rounded-md border border-brand-red-hover bg-brand-red px-3 py-1 font-mono text-[10px] font-bold tracking-wider text-text-primary uppercase">
                                    {{ $slide['part'] }}
                                </span>
                                <span class="font-mono text-4xl sm:text-5xl font-bold tracking-tighter text-brand-gold/35">
                                    {{ $slide['number'] }}
                                </span>
                            </div>

                            {{-- Wireframe 1:1 Box --}}
                            <div class="relative mx-auto my-4 flex aspect-square w-full max-w-[340px] items-center justify-center rounded-xl border border-brand-gold/30 bg-surface-card/75 p-6 backdrop-blur-xs">
                                <span class="absolute left-3 top-3 font-mono text-[9px] tracking-wider text-brand-gold">
                                    {{ $slide['wireframeLabel'] }}
                                </span>
                                <span class="absolute bottom-3 right-3 font-mono text-[9px] tracking-wider text-text-muted">
                                    ASSET_LAYER: {{ $slide['number'] }}_OF_05 // BEE-NER_2D
                                </span>

                                <img
                                    src="{{ asset($slide['image']) }}"
                                    alt="{{ $slide['title'] }}"
                                    class="relative max-h-full max-w-full object-contain p-4"
                                    loading="lazy"
                                />
                            </div>

                            {{-- Bottom Technical Label --}}
                            <div class="flex items-center justify-between font-mono text-[10px] uppercase tracking-wider text-text-muted">
                                <span>BEE-NER // CHARACTER STUDY</span>
                                <span class="text-brand-gold">[BINARY_BEE: ACTIVE]</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── RIGHT: ATTRIBUTE DOSSIER (50%) ── --}}
            <div class="flex flex-col justify-between bg-surface-card/95 p-6 sm:p-8 lg:p-10">
                <div>
                    <div class="mb-8 flex items-center justify-between border-b border-white/10 pb-4">
                        <p class="font-mono text-xs uppercase tracking-wider text-text-muted">
                            ATTRIBUTE BREAKDOWN
                        </p>
                        <span class="inline-flex items-center rounded-md bg-brand-gold/15 border border-brand-gold/40 px-3 py-1 font-mono text-xs font-bold text-brand-gold">
                            [ KOMPONEN <span class="text-text-primary mx-1" x-text="String(activeSlide).padStart(2, '0')">01</span> / 05 ]
                        </span>
                    </div>

                    <div class="grid">
                        @foreach ($slides as $slide)
                            <div
                                x-show="activeSlide === {{ $loop->iteration }}"
                                x-transition:enter="transition duration-300 ease-out"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition duration-150 ease-in"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="col-start-1 row-start-1 min-h-56"
                            >
                                <p class="font-mono text-xs font-bold uppercase tracking-wider text-brand-gold">
                                    {{ $slide['eyebrow'] }}
                                </p>
                                <h3 class="mt-3 max-w-lg font-display text-2xl sm:text-3xl font-bold leading-snug tracking-tight text-text-primary">
                                    {{ $slide['title'] }}
                                </h3>
                                <p class="mt-4 max-w-lg font-body text-sm sm:text-base leading-relaxed text-text-secondary">
                                    {{ $slide['description'] }}
                                </p>
                                <div class="mt-6">
                                    <span class="inline-flex items-center rounded-lg border border-brand-red-hover/60 bg-brand-red/25 px-4 py-2 font-mono text-xs font-semibold uppercase tracking-wider text-text-primary">
                                        {{ $slide['detail'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Interactive Navigation Controls --}}
                <div class="mt-8 flex items-center justify-between gap-4 border-t border-white/10 pt-6">
                    {{-- Indicator Dots --}}
                    <div class="flex items-center gap-2" role="tablist" aria-label="Pilih slide maskot">
                        @foreach ($slides as $slide)
                            <button
                                type="button"
                                class="h-2.5 rounded-full border transition-all duration-200 cursor-pointer"
                                :class="activeSlide === {{ $loop->iteration }}
                                    ? 'w-8 bg-brand-gold border-brand-gold'
                                    : 'w-2.5 bg-brand-red/60 border-brand-red-hover hover:bg-brand-red-hover'"
                                @click="goTo({{ $loop->iteration }})"
                                :aria-label="'Tampilkan slide {{ $loop->iteration }}'"
                                :aria-current="activeSlide === {{ $loop->iteration }} ? 'true' : 'false'"
                            ></button>
                        @endforeach
                    </div>

                    {{-- Prev / Next Buttons --}}
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="grid size-10 sm:size-11 place-items-center rounded-lg border border-white/15 bg-canvas-base text-text-primary transition hover:border-brand-gold hover:text-brand-gold active:scale-95 cursor-pointer"
                            @click="previous"
                            aria-label="Slide sebelumnya"
                        >
                            <span class="font-mono text-lg leading-none" aria-hidden="true">&larr;</span>
                        </button>
                        <button
                            type="button"
                            class="grid size-10 sm:size-11 place-items-center rounded-lg bg-brand-red border border-brand-red-hover text-text-primary font-semibold transition hover:bg-brand-red-hover active:scale-95 cursor-pointer glow-red-subtle"
                            @click="next"
                            aria-label="Slide berikutnya"
                        >
                            <span class="font-mono text-lg leading-none" aria-hidden="true">&rarr;</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
