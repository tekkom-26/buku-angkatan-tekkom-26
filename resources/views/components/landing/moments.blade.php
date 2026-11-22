@props([
    'moments' => [
        [
            'id' => 'moment-01',
            'category' => 'prestasi',
            'categoryLabel' => 'Prestasi',
            'badge' => '[PRESTASI // 2026]',
            'wireframeLabel' => '[X] [PHOTO_MOMENT_16:9 — INAUGURATION_EVENT]',
            'image' => '/assets/Placeholder.png',
            'title' => 'Inaugurasi & Pengukuhan Mahasiswa Baru Teknik Komputer 2026',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Momentum awal kebersamaan seluruh anggota koloni COMETS 2026 di lingkungan kampus Tembalang.',
            'meta' => 'SEMANGAT_TEKKOM // 24 OKT 2026',
            'nodeRef' => 'NODE_26:01',
        ],
        [
            'id' => 'moment-02',
            'category' => 'lab',
            'categoryLabel' => 'Kegiatan Lab',
            'badge' => '[KEGIATAN LAB // 2026]',
            'wireframeLabel' => '[X] [PHOTO_MOMENT_16:9 — HARDWARE_LAB_SESSION]',
            'image' => '/assets/Placeholder.png',
            'title' => 'Eksplorasi Praktikum Sistem Digital & Arsitektur Perangkat Keras',
            'description' => 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Kolaborasi pemecahan masalah teknis dan perakitan modul praktikum.',
            'meta' => 'LAB_ARSITEKTUR // 12 NOV 2026',
            'nodeRef' => 'NODE_26:02',
        ],
        [
            'id' => 'moment-03',
            'category' => 'kebersamaan',
            'categoryLabel' => 'Kebersamaan',
            'badge' => '[KEBERSAMAAN // 2026]',
            'wireframeLabel' => '[X] [PHOTO_MOMENT_16:9 — COHORT_GATHERING]',
            'image' => '/assets/Placeholder.png',
            'title' => 'Malam Keakraban & Forum Solidaritas Keluarga Besar COMETS 2026',
            'description' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Menyatukan visi lintas kelas dalam satu sarang koloni.',
            'meta' => 'TEMBALANG_CAMPUS // 05 DES 2026',
            'nodeRef' => 'NODE_26:03',
        ],
    ],
])

{{-- ==========================================================================
     HERO MEDIA CAROUSEL (16:9 / 21:9 Circuit-Framed Interactive Carousel)
     Mengacu pada .revamp-ui/PRD.md §3.1.1, DESIGN_TASTE.md & DESIGN_TOKENS.md
     ========================================================================== --}}
<div
    id="moments-section"
    aria-labelledby="moments-section-heading"
    x-data="{
        activeSlide: 0,
        totalSlides: {{ count($moments) }},
        nextSlide() {
            this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
        },
        prevSlide() {
            this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides;
        },
        goToSlide(index) {
            this.activeSlide = index;
        }
    }"
    class="w-full max-w-5xl mx-auto"
>
    <div class="sr-only">
        <h2 id="moments-section-heading">Dokumentasi & Kilas Balik Angkatan</h2>
        <span>// 03_ARCHIVE // MOMENTS</span>
    </div>

    {{-- Circuit-Framed Carousel Container (1px Hairline Border + Top Blood Red / Tech Gold Accent Bar) --}}
    <div class="relative rounded-xl border border-white/10 hover:border-brand-gold/40 bg-surface-card p-3 sm:p-4 lg:p-5 transition-colors overflow-hidden">

        {{-- Top Dual-Tone Circuit Line (Tech Gold + Blood Red) --}}
        <div class="absolute inset-x-0 top-0 h-1 flex" aria-hidden="true">
            <div class="w-2/3 bg-brand-gold"></div>
            <div class="w-1/3 bg-brand-red"></div>
        </div>

        {{-- Top Circuit Header Bar --}}
        <div class="flex items-center justify-between gap-2 pb-3 mb-3 pt-1 border-b border-white/10 font-mono text-[11px] uppercase tracking-wider">
            <div class="flex items-center gap-2 text-brand-gold">
                <span class="size-2 rounded-full bg-brand-red-hover" aria-hidden="true"></span>
                <span>&gt; COLONY_MOMENTS // ARCHIVE_STREAM</span>
            </div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-brand-red/30 border border-brand-red-hover/60 text-text-primary">
                <span>SLIDE_</span><span class="text-brand-gold font-bold" x-text="String(activeSlide + 1).padStart(2, '0')">01</span><span> / {{ str_pad((string) count($moments), 2, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        {{-- 16:9 / 21:9 Media Viewport --}}
        <div class="relative w-full aspect-video sm:aspect-[21/9] rounded-lg overflow-hidden border border-white/10 bg-canvas-base">
            @foreach ($moments as $index => $moment)
                <article
                    x-show="activeSlide === {{ $index }}"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0 flex flex-col justify-between p-4 sm:p-6"
                >
                    {{-- Background Placeholder Image --}}
                    <img
                        src="{{ asset($moment['image']) }}"
                        alt="{{ $moment['wireframeLabel'] }}"
                        class="absolute inset-0 w-full h-full object-contain p-8 opacity-65 select-none pointer-events-none"
                        loading="lazy"
                    />

                    {{-- Top Overlay Metadata Row --}}
                    <div class="relative z-10 flex items-center justify-between gap-2">
                        <span class="bg-brand-red border border-brand-red-hover text-text-primary font-mono text-[10px] sm:text-xs font-semibold uppercase px-3 py-1 rounded-md shadow-xs">
                            {{ $moment['badge'] }}
                        </span>
                        <span class="font-mono text-[10px] uppercase tracking-wider text-brand-gold bg-surface-card/90 px-2.5 py-1 rounded border border-brand-gold/30">
                            {{ $moment['nodeRef'] }}
                        </span>
                    </div>

                    {{-- Center Wireframe Label --}}
                    <div class="relative z-10 my-auto flex items-center justify-center pointer-events-none">
                        <span class="font-mono text-[10px] sm:text-xs uppercase tracking-wider text-text-secondary bg-canvas-base/80 border border-white/10 px-3 py-1.5 rounded-md">
                            {{ $moment['wireframeLabel'] }}
                        </span>
                    </div>

                    {{-- Bottom Caption Bar with Blood Red Left Accent Border --}}
                    <div class="relative z-10 rounded-r-lg rounded-l-sm bg-canvas-base/95 border border-white/10 border-l-4 border-l-brand-red p-3.5 sm:p-4 backdrop-blur-xs text-left">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <h3 class="font-display text-sm sm:text-base lg:text-lg font-bold text-text-primary leading-snug">
                                    {{ $moment['title'] }}
                                </h3>
                                <p class="font-body text-xs sm:text-sm text-text-secondary line-clamp-1 mt-0.5">
                                    {{ $moment['description'] }}
                                </p>
                            </div>
                            <span class="font-mono text-[10px] uppercase tracking-wider text-brand-gold shrink-0">
                                {{ $moment['meta'] }}
                            </span>
                        </div>
                    </div>
                </article>
            @endforeach

            {{-- Left / Right Navigation Arrows --}}
            <button
                type="button"
                @click="prevSlide()"
                aria-label="Slide momen sebelumnya"
                class="absolute left-3 top-1/2 -translate-y-1/2 z-20 size-9 sm:size-10 rounded-lg bg-surface-card/90 hover:bg-brand-red border border-white/15 hover:border-brand-red-hover text-text-primary flex items-center justify-center transition-colors cursor-pointer"
            >
                <span class="font-mono text-base leading-none" aria-hidden="true">&larr;</span>
            </button>
            <button
                type="button"
                @click="nextSlide()"
                aria-label="Slide momen berikutnya"
                class="absolute right-3 top-1/2 -translate-y-1/2 z-20 size-9 sm:size-10 rounded-lg bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary flex items-center justify-center transition-colors cursor-pointer shadow-xs"
            >
                <span class="font-mono text-base leading-none" aria-hidden="true">&rarr;</span>
            </button>
        </div>

        {{-- Interactive Dot Pagination Bar --}}
        <div class="flex items-center justify-between gap-4 pt-3 mt-3 border-t border-white/10">
            <span class="font-mono text-[10px] uppercase tracking-wider text-text-muted">
                CIRCUIT_FRAME // 21:9_VIEWPORT
            </span>

            <div class="flex items-center gap-2" role="tablist" aria-label="Navigasi slide momen angkatan">
                @foreach ($moments as $index => $moment)
                    <button
                        type="button"
                        role="tab"
                        @click="goToSlide({{ $index }})"
                        :aria-selected="activeSlide === {{ $index }} ? 'true' : 'false'"
                        :aria-label="'Tampilkan momen {{ $index + 1 }}'"
                        :class="activeSlide === {{ $index }}
                            ? 'w-7 bg-brand-gold border-brand-gold'
                            : 'w-2.5 bg-brand-red/60 border-brand-red-hover hover:bg-brand-red-hover'"
                        class="h-2.5 rounded-full border transition-all duration-200 cursor-pointer"
                    ></button>
                @endforeach
            </div>

            <span class="font-mono text-[10px] uppercase tracking-wider text-brand-gold">
                [HEX_GRID: SYNC]
            </span>
        </div>

    </div>
</div>
