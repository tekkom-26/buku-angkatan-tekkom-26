@props([
    'logoPlaceholder' => '/assets/Placeholder.png',
])

@php
    $slides = [
        [
            'number' => '01',
            'eyebrow' => 'ELEVATION LAYER',
            'title' => 'Sayap Ascent // Elevasi & Ambisi Melambung',
            'description' => 'Rentang sayap geometris melambangkan semangat elevasi (Ascent) dan ambisi angkatan 2026 untuk terus terbang tinggi melampaui batas kemampuan.',
            'detail' => 'Elevasi // Ambisi Melambung',
            'part' => 'WINGS',
            'accent' => 'highlight',
            'accentClass' => 'text-highlight-900',
            'wireframeLabel' => '[X] [ASSET: LOGO_COMPONENT_1:1 — ELEMEN_SAYAP_ASCENT]',
            'image' => $logoPlaceholder,
            // 'image' => '/assets/logo/sayap-ascent.png', // Uncomment & hapus baris di atas jika aset baru sudah siap
        ],
        [
            'number' => '02',
            'eyebrow' => 'COHORT IDENTITY',
            'title' => 'Geometri Angka 26 // Identitas Angkatan',
            'description' => 'Konstruksi garis tegas yang membentuk siluet angka 26 sebagai penanda abadi tahun kelahiran dan eksistensi keluarga besar Teknik Komputer 2026.',
            'detail' => 'Identitas // Angkatan 2026',
            'part' => 'NUMERAL 26',
            'accent' => 'secondary',
            'accentClass' => 'text-secondary-700',
            'wireframeLabel' => '[X] [ASSET: LOGO_COMPONENT_1:1 — GEOMETRI_ANGKA_26]',
            'image' => $logoPlaceholder,
            // 'image' => '/assets/logo/angka-26.png', // Uncomment & hapus baris di atas jika aset baru sudah siap
        ],
        [
            'number' => '03',
            'eyebrow' => 'ENGINEERING CORE',
            'title' => 'Sirkuit Logika Komputasi // Keahlian Rekayasa',
            'description' => 'Jalur sirkuit terpadu dan simpul logika merepresentasikan fondasi keilmuan Teknik Komputer yang menghubungkan perangkat keras dan perangkat lunak.',
            'detail' => 'Keahlian // Rekayasa Komputasi',
            'part' => 'CIRCUIT',
            'accent' => 'highlight',
            'accentClass' => 'text-highlight-900',
            'wireframeLabel' => '[X] [ASSET: LOGO_COMPONENT_1:1 — SIRKUIT_LOGIKA_KOMPUTASI]',
            'image' => $logoPlaceholder,
            // 'image' => '/assets/logo/sirkuit.png', // Uncomment & hapus baris di atas jika aset baru sudah siap
        ],
        [
            'number' => '04',
            'eyebrow' => 'UNIFIED EMBLEM',
            'title' => 'Lambang Utuh Terintegrasi // Solidaritas Kolektif',
            'description' => 'Perpaduan utuh seluruh elemen membentuk perisai kesatuan yang kokoh, menjadi simbol solidaritas kolektif yang tak terpisahkan.',
            'detail' => 'Solidaritas // Kesatuan Kolektif',
            'part' => 'FULL EMBLEM',
            'accent' => 'secondary',
            'accentClass' => 'text-secondary-700',
            'wireframeLabel' => '[X] [ASSET: LOGO_COMPONENT_1:1 — LAMBANG_UTUH_TERINTEGRASI]',
            'image' => $logoPlaceholder,
            // 'image' => '/assets/logo/full-emblem.png', // Uncomment & hapus baris di atas jika aset baru sudah siap
        ],
    ];
@endphp

<section
    {{ $attributes->merge(['class' => 'bg-surface-canvas text-primary-900']) }}
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
    x-cloak
>
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 md:py-20 lg:px-8">
        {{-- Section Header --}}
        <div class="mb-10 flex flex-col gap-5 border-b border-surface-border pb-8 md:mb-14 md:flex-row md:items-end md:justify-between">
            <div class="max-w-2xl">
                <p class="mb-3 font-mono text-xs uppercase tracking-[0.24em] text-secondary-500">// 03_LOGO_ANATOMY</p>
                <h2 class="text-[26px] font-bold tracking-tight text-primary-900 sm:text-4xl">
                    Bedah Simbolisme
                    <span class="block text-secondary-500">Logo ASCENT</span>
                </h2>
                <p class="mt-4 max-w-xl text-sm leading-relaxed text-primary-700 sm:text-base">
                    Setiap lapisan geometri pada lambang ASCENT dirancang dengan presisi untuk merepresentasikan visi, identitas angkatan, keahlian rekayasa, dan solidaritas Teknik Komputer 2026.
                </p>
            </div>
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-primary-500">LOGO / 04 LAYERS</p>
        </div>

        {{-- Split Carousel Container (50% Visual Left / 50% Dossier Right) --}}
        <div class="grid overflow-hidden rounded-2xl border border-surface-border bg-surface-card p-0 shadow-xs lg:grid-cols-2">
            {{-- Left: Visual Display (50%) --}}
            <div class="relative min-h-[22rem] overflow-hidden bg-primary-900 p-6 sm:min-h-[30rem] sm:p-8">
                <div class="absolute -right-20 -top-20 size-64 rounded-full border border-white/15 pointer-events-none"></div>
                <div class="absolute -bottom-28 -left-20 size-80 rounded-full border border-white/10 pointer-events-none"></div>

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
                            class="col-start-1 row-start-1 flex h-full min-h-[20rem] flex-col justify-between sm:min-h-[26rem]"
                        >
                            {{-- Top Metadata Row --}}
                            <div class="flex items-start justify-between">
                                <span class="rounded-lg border border-white/40 bg-white/10 px-3 py-1 font-mono text-[10px] tracking-[0.2em] text-white uppercase">{{ $slide['part'] }}</span>
                                <span class="font-mono text-5xl font-bold tracking-[-0.08em] text-white/30">{{ $slide['number'] }}</span>
                            </div>

                            {{-- Wireframe 1:1 Box (400x400px) --}}
                            <div class="relative mx-auto flex aspect-square w-full max-w-[400px] items-center justify-center rounded-xl border border-white/30 bg-white/5 p-5 sm:p-8">
                                <span class="absolute left-3 top-3 font-mono text-[9px] tracking-[0.14em] text-white/80">{{ $slide['wireframeLabel'] }}</span>
                                <span class="absolute bottom-3 right-3 font-mono text-[9px] tracking-[0.14em] text-white/80">ASSET_LAYER: {{ $slide['number'] }}_OF_04 // VECTOR_CONSTRUCT</span>

                                <img
                                    src="{{ asset($slide['image']) }}"
                                    alt="{{ $slide['title'] }}"
                                    class="relative max-h-full max-w-full object-contain"
                                    loading="lazy"
                                />
                            </div>

                            {{-- Bottom Technical Label --}}
                            <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/75">ASCENT 2026 // SYMBOLISM STUDY</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: Philosophy Dossier (50%) --}}
            <div class="flex flex-col justify-between bg-surface-card p-6 sm:p-8 lg:p-10">
                <div>
                    {{-- Dossier Header & Slide Counter Badge --}}
                    <div class="mb-10 flex items-center justify-between border-b border-surface-border pb-5">
                        <p class="font-mono text-xs uppercase tracking-[0.2em] text-primary-500">LAYER BREAKDOWN</p>
                        <span class="inline-flex items-center rounded-lg bg-highlight-500 px-3 py-1 font-mono text-xs font-semibold text-primary-900">
                            [ KOMPONEN <span x-text="String(activeSlide).padStart(2, '0')">01</span> / 04 ]
                        </span>
                    </div>

                    {{-- Dossier Content (Grid stacked to prevent layout shift during transition) --}}
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
                                <p class="font-mono text-xs font-bold uppercase tracking-[0.22em] {{ $slide['accentClass'] }}">
                                    {{ $slide['eyebrow'] }}
                                </p>
                                <h3 class="mt-4 max-w-lg text-2xl font-bold leading-snug tracking-tight text-primary-900 sm:text-3xl">
                                    {{ $slide['title'] }}
                                </h3>
                                <p class="mt-5 max-w-lg text-sm leading-relaxed text-primary-700 sm:text-base">
                                    {{ $slide['description'] }}
                                </p>
                                <div class="mt-8">
                                    <span class="inline-flex items-center rounded-lg border border-surface-border bg-white/70 px-4 py-2 font-mono text-xs font-medium uppercase tracking-wider text-primary-700">
                                        {{ $slide['detail'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Interactive Navigation Controls --}}
                <div class="mt-10 flex items-center justify-between gap-5 border-t border-surface-border pt-6">
                    {{-- Indicator Dot Pills --}}
                    <div class="flex items-center gap-2" role="tablist" aria-label="Pilih slide komponen logo">
                        @foreach ($slides as $slide)
                            <button
                                type="button"
                                class="h-2.5 rounded-full transition-all duration-300"
                                :class="activeSlide === {{ $loop->iteration }} ? 'w-8 bg-secondary-500' : 'w-2.5 bg-primary-900/20 hover:bg-primary-900/40'"
                                @click="goTo({{ $loop->iteration }})"
                                :aria-label="'Tampilkan slide logo {{ $loop->iteration }}'"
                                :aria-current="activeSlide === {{ $loop->iteration }} ? 'true' : 'false'"
                            ></button>
                        @endforeach
                    </div>

                    {{-- Prev / Next Buttons (Monoline Arrows, rounded-lg) --}}
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="grid size-11 place-items-center rounded-lg border border-surface-border bg-white/40 text-primary-900 transition hover:bg-white active:scale-95"
                            @click="previous"
                            aria-label="Slide logo sebelumnya"
                        >
                            <span class="text-xl leading-none" aria-hidden="true">&larr;</span>
                        </button>
                        <button
                            type="button"
                            class="grid size-11 place-items-center rounded-lg bg-secondary-500 text-primary-900 font-semibold transition hover:bg-secondary-700 hover:text-white active:scale-95"
                            @click="next"
                            aria-label="Slide logo berikutnya"
                        >
                            <span class="text-xl leading-none" aria-hidden="true">&rarr;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
