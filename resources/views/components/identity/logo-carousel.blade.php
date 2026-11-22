@props([
    'content' => null,
    'logoPlaceholder' => '/assets/Placeholder.png',
])

@php
    $c = $content ?? [];
    $eyebrow = $c['eyebrow'] ?? '// 03_LOGO_ANATOMY';
    $title = $c['title'] ?? 'Bedah Anatomi';
    $subtitle = $c['subtitle'] ?? '5 Elemen Lambang COMETS';
    $description = $c['description'] ?? 'Setiap lapisan geometri pada lambang COMETS 2026 dirancang dengan presisi untuk memadukan solidaritas koloni lebah, ketangguhan melewati masa terang dan gelap, serta identitas keilmuan Teknik Komputer.';

    $cometsLogoSlides = [
        [
            'number' => '01',
            'eyebrow' => 'ELEMEN 01 // EQUALITY & PROTECTION',
            'title' => 'Lingkaran // Kesetaraan Tanpa Awal & Akhir',
            'description' => 'Lingkaran utuh melambangkan kesetaraan seluruh anggota angkatan tanpa awal maupun akhir, sebuah ikatan pelindung di mana setiap mahasiswa saling menjaga satu sama lain.',
            'detail' => 'Kesetaraan // Saling Melindungi',
            'part' => 'LINGKARAN',
            'accent' => 'gold',
            'wireframeLabel' => '[X: COMETS Logo — Elemen 01 Lingkaran]',
            'image' => $logoPlaceholder,
        ],
        [
            'number' => '02',
            'eyebrow' => 'ELEMEN 02 // COLONY SOLIDARITY',
            'title' => 'Lebah // Satu Koloni Membangun Sarang Kokoh',
            'description' => 'Lebah merepresentasikan nilai inti Kebersamaan: satu koloni pekerja keras yang bersinergi secara harmonis untuk membangun sarang dan masa depan yang kokoh.',
            'detail' => 'Kebersamaan // Solidaritas Koloni',
            'part' => 'LEBAH',
            'accent' => 'red',
            'wireframeLabel' => '[X: COMETS Logo — Elemen 02 Lebah]',
            'image' => $logoPlaceholder,
        ],
        [
            'number' => '03',
            'eyebrow' => 'ELEMEN 03 // COMPUTER ENGINEERING CORE',
            'title' => 'Sayap Bersirkuit // Identitas Teknik Komputer & Daya Juang',
            'description' => 'Rentang sayap berjalur sirkuit terpadu melambangkan identitas keilmuan Teknik Komputer (perangkat keras & perangkat lunak) sekaligus daya juang untuk terus melaju tinggi.',
            'detail' => 'Teknik Komputer // Daya Juang Melaju',
            'part' => 'SAYAP BERSIRKUIT',
            'accent' => 'gold',
            'wireframeLabel' => '[X: COMETS Logo — Elemen 03 Sayap Bersirkuit]',
            'image' => $logoPlaceholder,
        ],
        [
            'number' => '04',
            'eyebrow' => 'ELEMEN 04 // RESILIENCE & GRIT',
            'title' => 'Belang & Sengat // Masa Terang-Gelap & Ketangguhan',
            'description' => 'Perpaduan belang hitam-emas dan sengat tajam melambangkan masa terang maupun gelap yang dilewati bersama dengan penuh keberanian dan daya tahan (Nilai Inti: Ketangguhan).',
            'detail' => 'Ketangguhan // Terang & Gelap Bersama',
            'part' => 'BELANG & SENGAT',
            'accent' => 'red',
            'wireframeLabel' => '[X: COMETS Logo — Elemen 04 Belang & Sengat]',
            'image' => $logoPlaceholder,
        ],
        [
            'number' => '05',
            'eyebrow' => 'ELEMEN 05 // COHORT 2026 MATRIX',
            'title' => '26 Segi Enam // 26 Simpul Terhubung Membentuk Rumah Bersama',
            'description' => 'Konstruksi 26 sel heksagonal menjadi penanda abadi angkatan 2026, di mana setiap simpul mahasiswa saling terhubung membentuk satu rumah besar Teknik Komputer Undip.',
            'detail' => 'Angkatan 2026 // 26 Simpul Hexagon',
            'part' => '26 SEGI ENAM',
            'accent' => 'gold',
            'wireframeLabel' => '[X: COMETS Logo — Elemen 05 26 Segi Enam]',
            'image' => $logoPlaceholder,
        ],
    ];

    $slides = (! empty($c['slides']) && count($c['slides']) === 5) ? $c['slides'] : $cometsLogoSlides;
@endphp

{{-- ==========================================================================
     IDENTITY — SECTION 3: LOGO EXPLORER (5 ELEMEN LAMBANG COMETS)
     Mengacu pada .revamp-ui/PRD.md §3.2.3, DESIGN_TASTE.md & DESIGN_TOKENS.md
     ========================================================================== --}}
<section
    id="identity-logo-section"
    aria-labelledby="logo-explorer-heading"
    {{ $attributes->merge(['class' => 'relative w-full bg-ambient-matrix text-text-primary py-12 sm:py-16 lg:py-20 border-t border-white/10 overflow-hidden']) }}
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
                    &gt; {{ $eyebrow }} // 05_ELEMENTS
                </p>
                <h2 id="logo-explorer-heading" class="font-display text-2xl sm:text-4xl font-bold tracking-tight text-text-primary">
                    {{ $title }}
                    <span class="text-brand-gold">{{ $subtitle }}</span>
                </h2>
                <p class="mt-3 max-w-xl font-body text-sm sm:text-base leading-relaxed text-text-secondary">
                    {{ $description }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 rounded-md bg-brand-gold/15 border border-brand-gold/40 font-mono text-xs uppercase tracking-wider text-brand-gold font-semibold">
                    LOGO / 05 LAYERS
                </span>
            </div>
        </div>

        {{-- Quick Element Selector Pills (5 Interactive Tabs) --}}
        <div class="flex flex-wrap items-center gap-2 mb-6" role="tablist" aria-label="Pilih elemen anatomi logo COMETS">
            @foreach ($slides as $slide)
                <button
                    type="button"
                    role="tab"
                    @click="goTo({{ $loop->iteration }})"
                    :aria-selected="activeSlide === {{ $loop->iteration }} ? 'true' : 'false'"
                    :class="activeSlide === {{ $loop->iteration }}
                        ? 'bg-brand-red border-brand-red-hover text-text-primary font-semibold glow-red-subtle'
                        : 'bg-surface-card/80 border-white/10 text-text-secondary hover:border-brand-gold/40 hover:text-text-primary'"
                    class="px-3.5 py-2 rounded-lg border font-mono text-xs uppercase tracking-wider transition-all duration-200 cursor-pointer inline-flex items-center gap-2"
                >
                    <span class="text-brand-gold font-bold">{{ $slide['number'] }}.</span>
                    <span>{{ $slide['part'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- Split Carousel Container (50% Visual Left / 50% Dossier Right) --}}
        <div class="grid overflow-hidden rounded-xl border border-brand-gold/30 bg-surface-card/90 shadow-lg lg:grid-cols-2 glow-gold-subtle">

            {{-- ── LEFT: VISUAL DISPLAY VIEWPORT (50%) ── --}}
            <div class="relative min-h-[22rem] overflow-hidden bg-canvas-base p-6 sm:min-h-[28rem] sm:p-8 border-b lg:border-b-0 lg:border-r border-white/10">
                {{-- Top Dual Circuit Line --}}
                <div class="absolute inset-x-0 top-0 h-1 flex" aria-hidden="true">
                    <div class="w-1/2 bg-brand-gold"></div>
                    <div class="w-1/2 bg-brand-red"></div>
                </div>

                {{-- Decorative Concentric Hexagons in Viewport Background --}}
                <svg class="pointer-events-none absolute inset-0 m-auto size-80 text-brand-gold/10" viewBox="0 0 200 200" fill="none" aria-hidden="true">
                    <polygon points="100,10 178,55 178,145 100,190 22,145 22,55" stroke="currentColor" stroke-width="1" />
                    <polygon points="100,35 156,67 156,133 100,165 44,133 44,67" stroke="currentColor" stroke-width="1" stroke-dasharray="4 4" />
                </svg>

                <div class="relative z-10 grid h-full">
                    @foreach ($slides as $slide)
                        <div
                            x-show="activeSlide === {{ $loop->iteration }}"
                            x-transition:enter="transition duration-300 ease-out"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition duration-200 ease-in"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="col-start-1 row-start-1 flex h-full min-h-[20rem] flex-col justify-between sm:min-h-[25rem]"
                        >
                            {{-- Top Metadata Row --}}
                            <div class="flex items-start justify-between">
                                <span class="rounded-md border px-3 py-1 font-mono text-[10px] font-bold tracking-wider uppercase {{ ($slide['accent'] ?? 'gold') === 'red' ? 'border-brand-red-hover bg-brand-red text-text-primary' : 'border-brand-gold/40 bg-brand-gold/15 text-brand-gold' }}">
                                    {{ $slide['part'] }}
                                </span>
                                <span class="font-mono text-4xl sm:text-5xl font-bold tracking-tighter text-brand-gold/30">
                                    {{ $slide['number'] }}
                                </span>
                            </div>

                            {{-- Wireframe 1:1 Box --}}
                            <div class="relative mx-auto my-4 flex aspect-square w-full max-w-[340px] items-center justify-center rounded-xl border border-brand-gold/30 bg-surface-card/70 p-6">
                                <span class="absolute left-3 top-3 font-mono text-[9px] tracking-wider text-brand-gold">
                                    {{ $slide['wireframeLabel'] }}
                                </span>
                                <span class="absolute bottom-3 right-3 font-mono text-[9px] tracking-wider text-text-muted">
                                    ASSET_LAYER: {{ $slide['number'] }}_OF_05 // COMETS_VECTOR
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
                                <span>COMETS 2026 // LOGO ANATOMY</span>
                                <span class="text-brand-gold">[NODE_SYNC: OK]</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ── RIGHT: PHILOSOPHY DOSSIER (50%) ── --}}
            <div class="flex flex-col justify-between bg-surface-card/95 p-6 sm:p-8 lg:p-10">
                <div>
                    {{-- Dossier Header & Slide Counter Badge --}}
                    <div class="mb-8 flex items-center justify-between border-b border-white/10 pb-4">
                        <p class="font-mono text-xs uppercase tracking-wider text-text-muted">
                            LAYER BREAKDOWN
                        </p>
                        <span class="inline-flex items-center rounded-md bg-brand-red border border-brand-red-hover px-3 py-1 font-mono text-xs font-bold text-text-primary">
                            [ KOMPONEN <span class="text-brand-gold mx-1" x-text="String(activeSlide).padStart(2, '0')">01</span> / 05 ]
                        </span>
                    </div>

                    {{-- Dossier Content --}}
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
                                    <span class="inline-flex items-center rounded-lg border border-brand-gold/30 bg-canvas-base/80 px-4 py-2 font-mono text-xs font-semibold uppercase tracking-wider text-brand-gold">
                                        {{ $slide['detail'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Interactive Navigation Controls --}}
                <div class="mt-8 flex items-center justify-between gap-4 border-t border-white/10 pt-6">
                    {{-- Indicator Dot Pills --}}
                    <div class="flex items-center gap-2" role="tablist" aria-label="Pilih slide komponen logo">
                        @foreach ($slides as $slide)
                            <button
                                type="button"
                                class="h-2.5 rounded-full border transition-all duration-200 cursor-pointer"
                                :class="activeSlide === {{ $loop->iteration }}
                                    ? 'w-8 bg-brand-gold border-brand-gold'
                                    : 'w-2.5 bg-brand-red/60 border-brand-red-hover hover:bg-brand-red-hover'"
                                @click="goTo({{ $loop->iteration }})"
                                :aria-label="'Tampilkan slide logo {{ $loop->iteration }}'"
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
                            aria-label="Slide logo sebelumnya"
                        >
                            <span class="font-mono text-lg leading-none" aria-hidden="true">&larr;</span>
                        </button>
                        <button
                            type="button"
                            class="grid size-10 sm:size-11 place-items-center rounded-lg bg-brand-red border border-brand-red-hover text-text-primary font-semibold transition hover:bg-brand-red-hover active:scale-95 cursor-pointer glow-red-subtle"
                            @click="next"
                            aria-label="Slide logo berikutnya"
                        >
                            <span class="font-mono text-lg leading-none" aria-hidden="true">&rarr;</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
