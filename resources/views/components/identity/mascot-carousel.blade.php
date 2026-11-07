@props([
    'content' => null,
    'mascotPlaceholder' => '/assets/Placeholder.png',
])

@php
    $c = $content ?? [];
    $eyebrow = $c['eyebrow'] ?? '// 04_MASCOT_ANATOMY';
    $title = $c['title'] ?? 'Bedah Karakter';
    $subtitle = $c['subtitle'] ?? '& Anatomi Maskot';
    $description = $c['description'] ?? 'Setiap bagian dari gajah Tekkom membawa cerita tentang cara kita berpikir, terhubung, dan tumbuh sebagai satu angkatan.';
    $slides = $c['slides'] ?? [
        [
            'number' => '01',
            'eyebrow' => 'CORE ATTRIBUTE',
            'title' => 'Gading Komputasi',
            'description' => 'Gading melambangkan memori dan daya ingat kuat: kemampuan menyimpan pengalaman serta mengubahnya menjadi solusi.',
            'detail' => 'Memori // Daya Ingat Kuat',
            'part' => 'TUSK',
            'accent' => 'highlight',
            'accentClass' => 'text-highlight-900',
            'wireframeLabel' => '[X] [ASSET: MASCOT_COMPONENT_1:1 — ANATOMI_GADING]',
            'image' => $mascotPlaceholder,
            // 'image' => '/assets/mascot/gading.png', // Uncomment & hapus baris di atas jika aset baru sudah siap
        ],
        [
            'number' => '02',
            'eyebrow' => 'CORE ATTRIBUTE',
            'title' => 'Belalai Ramah',
            'description' => 'Belalai menjadi simbol sinergi dan komunikasi tanpa sekat: lentur, adaptif, dan selalu siap menjangkau satu sama lain.',
            'detail' => 'Sinergi // Komunikasi Tanpa Sekat',
            'part' => 'TRUNK',
            'accent' => 'secondary',
            'accentClass' => 'text-secondary-700',
            'wireframeLabel' => '[X] [ASSET: MASCOT_COMPONENT_1:1 — ANATOMI_BELALAI]',
            'image' => $mascotPlaceholder,
            // 'image' => '/assets/mascot/belalai.png', // Uncomment & hapus baris di atas jika aset baru sudah siap
        ],
        [
            'number' => '03',
            'eyebrow' => 'IDENTITY HARDWARE',
            'title' => 'Almamater / Hardware',
            'description' => 'Almamater adalah hardware identitas Teknik Komputer yang menyatukan karakter personal dengan rumah besar Tekkom.',
            'detail' => 'Identitas // Teknik Komputer',
            'part' => 'JACKET',
            'accent' => 'highlight',
            'accentClass' => 'text-highlight-900',
            'wireframeLabel' => '[X] [ASSET: MASCOT_COMPONENT_1:1 — ANATOMI_ALMAMATER]',
            'image' => $mascotPlaceholder,
            // 'image' => '/assets/mascot/almamater.png', // Uncomment & hapus baris di atas jika aset baru sudah siap
        ],
        [
            'number' => '04',
            'eyebrow' => 'FULL CHARACTER',
            'title' => 'Pose Penuh Karakter',
            'description' => 'Seluruh elemen bertemu dalam pose penuh karakter gajah chibi, simbol semangat angkatan Teknik Komputer 2026.',
            'detail' => 'Semangat // Angkatan 2026',
            'part' => 'FULL BODY',
            'accent' => 'secondary',
            'accentClass' => 'text-secondary-700',
            'wireframeLabel' => '[X] [ASSET: MASCOT_COMPONENT_1:1 — POSE_LENGKAP_CHIBI]',
            'image' => $mascotPlaceholder,
            // 'image' => '/assets/mascot/full-character.png', // Uncomment & hapus baris di atas jika aset baru sudah siap
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
                <p class="mb-3 font-mono text-xs uppercase tracking-[0.24em] text-secondary-500">{{ $eyebrow }}</p>
                <h2 class="text-3xl font-extrabold tracking-tight text-primary-900 sm:text-4xl">
                    {{ $title }}
                    <span class="block text-secondary-500">{!! $subtitle !!}</span>
                </h2>
                <p class="mt-4 max-w-xl text-sm leading-relaxed text-primary-700 sm:text-base">
                    {{ $description }}
                </p>
            </div>
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-primary-500">MASCOT / 04 ATTRIBUTES</p>
        </div>

        <div class="grid overflow-hidden rounded-2xl border border-surface-border bg-surface-card p-0 shadow-lg lg:grid-cols-2">
            {{-- Left: Visual Display (50%) --}}
            <div class="relative min-h-[22rem] overflow-hidden bg-secondary-500 p-6 sm:min-h-[30rem] sm:p-8">
                <div class="absolute -right-20 -top-20 size-64 rounded-full border border-white/20 pointer-events-none"></div>
                <div class="absolute -bottom-28 -left-20 size-80 rounded-full border border-white/15 pointer-events-none"></div>

                <div class="relative z-10 grid h-full">
                    @foreach ($slides as $slide)
                        <div
                            x-show="activeSlide === {{ $loop->iteration }}"
                            x-transition:enter="transition duration-300 ease-out"
                            x-transition:enter-start="translate-x-4 opacity-0"
                            x-transition:enter-end="translate-x-0 opacity-100"
                            x-transition:leave="transition duration-200 ease-in"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="col-start-1 row-start-1 flex h-full min-h-[20rem] flex-col justify-between sm:min-h-[26rem]"
                        >
                            {{-- Top Metadata Row --}}
                            <div class="flex items-start justify-between">
                                <span class="rounded-full border border-white/50 bg-white/10 px-3 py-1 font-mono text-[10px] tracking-[0.2em] text-white uppercase">{{ $slide['part'] }}</span>
                                <span class="font-mono text-5xl font-bold tracking-[-0.08em] text-white/40">{{ $slide['number'] }}</span>
                            </div>

                            {{-- Wireframe 1:1 Box --}}
                            <div class="relative mx-auto flex aspect-square w-full max-w-[380px] items-center justify-center rounded-xl border border-white/35 bg-white/10 p-5 backdrop-blur-xs sm:p-8">
                                <span class="absolute left-3 top-3 font-mono text-[9px] tracking-[0.16em] text-white/80">{{ $slide['wireframeLabel'] }}</span>
                                <span class="absolute bottom-3 right-3 font-mono text-[9px] tracking-[0.16em] text-white/80">ASSET_LAYER: {{ $slide['number'] }}_OF_04 // VECTOR_CONSTRUCT</span>

                                <img
                                    src="{{ asset($slide['image']) }}"
                                    alt="{{ $slide['title'] }}"
                                    class="relative max-h-full max-w-full object-contain drop-shadow-md"
                                    loading="lazy"
                                />
                            </div>

                            {{-- Bottom Technical Label --}}
                            <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/80">TEKKOM 2026 // CHARACTER STUDY</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col justify-between bg-surface-card p-6 sm:p-8 lg:p-10">
                <div>
                    <div class="mb-10 flex items-center justify-between border-b border-surface-border pb-5">
                        <p class="font-mono text-xs uppercase tracking-[0.2em] text-primary-500">ATTRIBUTE BREAKDOWN</p>
                        <span class="inline-flex items-center rounded bg-highlight-500 px-3 py-1 font-mono text-xs font-semibold text-primary-900">
                            [ KOMPONEN <span x-text="String(activeSlide).padStart(2, '0')">01</span> / 04 ]
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
                                <p class="font-mono text-xs font-bold uppercase tracking-[0.22em] {{ $slide['accentClass'] }}">
                                    {{ $slide['eyebrow'] }}
                                </p>
                                <h3 class="mt-4 max-w-md text-3xl font-extrabold leading-tight tracking-tight text-primary-900 sm:text-4xl">
                                    {{ $slide['title'] }}
                                </h3>
                                <p class="mt-5 max-w-md text-sm leading-relaxed text-primary-700 sm:text-base">
                                    {{ $slide['description'] }}
                                </p>
                                <div class="mt-8">
                                    <span class="inline-flex items-center rounded-full border border-surface-border bg-white/70 px-4 py-2 font-mono text-xs font-medium uppercase tracking-wider text-primary-700">
                                        {{ $slide['detail'] }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Interactive Navigation Controls --}}
                <div class="mt-10 flex items-center justify-between gap-5 border-t border-surface-border pt-6">
                    {{-- Indicator Dots --}}
                    <div class="flex items-center gap-2" role="tablist" aria-label="Pilih slide maskot">
                        @foreach ($slides as $slide)
                            <button
                                type="button"
                                class="h-2.5 rounded-full transition-all duration-300"
                                :class="activeSlide === {{ $loop->iteration }} ? 'w-8 bg-secondary-500' : 'w-2.5 bg-primary-900/20 hover:bg-primary-900/40'"
                                @click="goTo({{ $loop->iteration }})"
                                :aria-label="'Tampilkan slide {{ $loop->iteration }}'"
                                :aria-current="activeSlide === {{ $loop->iteration }} ? 'true' : 'false'"
                            ></button>
                        @endforeach
                    </div>

                    {{-- Prev / Next Buttons (Monoline Arrows) --}}
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="grid size-11 place-items-center rounded-full border border-surface-border bg-white/40 text-primary-900 transition hover:bg-white active:scale-95"
                            @click="previous"
                            aria-label="Slide sebelumnya"
                        >
                            <span class="text-xl leading-none" aria-hidden="true">&larr;</span>
                        </button>
                        <button
                            type="button"
                            class="grid size-11 place-items-center rounded-full bg-primary-900 text-white transition hover:bg-secondary-500 hover:text-primary-900 active:scale-95"
                            @click="next"
                            aria-label="Slide berikutnya"
                        >
                            <span class="text-xl leading-none" aria-hidden="true">&rarr;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
