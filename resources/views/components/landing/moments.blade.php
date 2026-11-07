@props([
    'moments' => [
        [
            'id' => 'moment-01',
            'category' => 'prestasi',
            'categoryLabel' => 'Prestasi',
            'badge' => '[PRESTASI // 2026]',
            'wireframeLabel' => '[X] [PHOTO_MOMENT_16:9 — INAUGURATION_EVENT]',
            // TODO: Ganti '/assets/Placeholder.png' dengan path foto dokumentasi asli (misal: '/assets/moments/inauguration.jpg')
            'image' => '/assets/Placeholder.png',
            'title' => 'Inaugurasi & Pengukuhan Mahasiswa Baru Teknik Komputer 2026',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Momentum awal kebersamaan seluruh anggota angkatan di lingkungan kampus Tembalang.',
            'meta' => 'SEMANGAT_TEKKOM // 24 OKT 2026',
            'nodeRef' => 'NODE_24:665',
        ],
        [
            'id' => 'moment-02',
            'category' => 'lab',
            'categoryLabel' => 'Kegiatan Lab',
            'badge' => '[KEGIATAN LAB // 2026]',
            'wireframeLabel' => '[X] [PHOTO_MOMENT_16:9 — HARDWARE_LAB_SESSION]',
            // TODO: Ganti '/assets/Placeholder.png' dengan path foto kegiatan lab asli (misal: '/assets/moments/hardware-lab.jpg')
            'image' => '/assets/Placeholder.png',
            'title' => 'Eksplorasi Praktikum Sistem Digital & Arsitektur Perangkat Keras',
            'description' => 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Kolaborasi pemecahan masalah teknis dan perakitan modul praktikum.',
            'meta' => 'LAB_ARSITEKTUR // 12 NOV 2026',
            'nodeRef' => 'NODE_24:691',
        ],
        [
            'id' => 'moment-03',
            'category' => 'kebersamaan',
            'categoryLabel' => 'Kebersamaan',
            'badge' => '[KEBERSAMAAN // 2026]',
            'wireframeLabel' => '[X] [PHOTO_MOMENT_16:9 — COHORT_GATHERING]',
            // TODO: Ganti '/assets/Placeholder.png' dengan path foto kebersamaan angkatan asli (misal: '/assets/moments/cohort-gathering.jpg')
            'image' => '/assets/Placeholder.png',
            'title' => 'Malam Keakraban & Forum Solidaritas Keluarga Besar Angkatan',
            'description' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Menyatukan visi lintas kelas.',
            'meta' => 'TEMBALANG_CAMPUS // 05 DES 2026',
            'nodeRef' => 'NODE_24:717',
        ],
    ],
])

{{-- ==========================================================================
     SECTION 3: MOMENTS & HIGHLIGHTS CAROUSEL (16:9 Photos & Filter Bar)
     Mengacu pada GitHub Issue #20 & #12, DESIGN.md, dan DOCS_COLOR_CONVENTION.md
     Figma Reference: Node 24:642 (Carousel Section) & Node 33:704 (Moments Card)
     ========================================================================== --}}
<section
    id="moments-section"
    aria-labelledby="moments-section-heading"
    x-data="{
        activeFilter: 'all',
        filters: [
            { key: 'all', label: 'Semua' },
            { key: 'prestasi', label: 'Prestasi' },
            { key: 'lab', label: 'Kegiatan Lab' },
            { key: 'kebersamaan', label: 'Kebersamaan' },
        ],
        setFilter(key) {
            this.activeFilter = key;
        }
    }"
    class="w-full bg-surface-canvas py-6 sm:py-10"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section Header Bar: Title on Left, Reactive Category Filter Chips on Right --}}
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-5 mb-7 sm:mb-8">

            <div>
                <div class="flex items-center gap-2 mb-2.5">
                    <span class="inline-block size-2 rounded-full bg-secondary-500" aria-hidden="true"></span>
                    <span class="font-mono text-[11px] sm:text-xs font-bold uppercase tracking-[0.05em] text-primary-700">
                        // 03_ARCHIVE // MOMENTS
                    </span>
                </div>
                <h2
                    id="moments-section-heading"
                    class="text-2xl sm:text-[28px] font-bold text-primary-900 tracking-[-0.02em] leading-tight"
                >
                    Dokumentasi & Kilas Balik Angkatan
                </h2>
            </div>

            {{-- Reactive Category Filter Bar (Alpine.js) --}}
            <div
                role="tablist"
                aria-label="Filter kategori dokumentasi angkatan"
                class="flex flex-wrap items-center gap-2"
            >
                <template x-for="filter in filters" :key="filter.key">
                    <button
                        type="button"
                        role="tab"
                        :aria-selected="activeFilter === filter.key ? 'true' : 'false'"
                        @click="setFilter(filter.key)"
                        :class="activeFilter === filter.key
                            ? 'bg-secondary-500 text-primary-900 border-primary-900/20 font-semibold shadow-2xs'
                            : 'bg-surface-card text-primary-700 border-surface-border hover:bg-surface-subtle hover:text-primary-900'"
                        class="px-3.5 py-2 rounded-lg border text-xs sm:text-sm font-medium transition-all duration-180 cursor-pointer focus-visible:outline-2 focus-visible:outline-secondary-500 focus-visible:outline-offset-2"
                    >
                        <span x-text="filter.label"></span>
                    </button>
                </template>
            </div>

        </div>

        {{-- 3-Card Carousel Grid (Desktop 3-Col Grid, Mobile Horizontal Snap / Stacked) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @foreach ($moments as $moment)
                <article
                    x-show="activeFilter === 'all' || activeFilter === '{{ $moment['category'] }}'"
                    x-transition.opacity.duration.200ms
                    class="group bg-white border border-surface-border rounded-2xl p-4 sm:p-5 flex flex-col justify-between gap-4 shadow-xs hover:border-primary-900/30 transition-all duration-200"
                >
                    <div>
                        {{-- Strict 16:9 Photo Wireframe Slot --}}
                        <div class="relative w-full aspect-video rounded-xl overflow-hidden border border-surface-border bg-surface-card mb-4 flex flex-col justify-between p-3">
                            {{-- Top Overlay Bar inside 16:9 Frame --}}
                            <div class="relative z-10 flex items-center justify-between gap-2">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-highlight-500 text-primary-900 font-mono text-[10px] font-bold uppercase tracking-wider shadow-2xs">
                                    {{ $moment['badge'] }}
                                </span>
                                <span class="font-mono text-[9px] uppercase tracking-wider text-primary-500 bg-white/90 px-2 py-0.5 rounded border border-surface-border">
                                    {{ $moment['nodeRef'] }}
                                </span>
                            </div>

                            {{-- Placeholder Image inside 16:9 Container --}}
                            <div class="relative flex-1 w-full flex items-center justify-center overflow-hidden my-1">
                                <img
                                    src="{{ asset($moment['image']) }}"
                                    alt="{{ $moment['wireframeLabel'] }}"
                                    class="w-full h-full object-contain select-none group-hover:scale-[1.02] transition-transform duration-200"
                                    loading="lazy"
                                />
                            </div>

                            {{-- Bottom Wireframe Identifier Label --}}
                            <div class="relative z-10 flex items-center justify-between gap-2">
                                <span class="font-mono text-[9px] uppercase tracking-wider text-primary-700 truncate">
                                    {{ $moment['wireframeLabel'] }}
                                </span>
                            </div>
                        </div>

                        {{-- Card Headline Caption --}}
                        <h3 class="text-base sm:text-[17px] font-bold text-primary-900 leading-snug mb-2">
                            {{ $moment['title'] }}
                        </h3>

                        {{-- Brief Editorial Description --}}
                        <p class="text-xs sm:text-sm text-primary-900/80 leading-relaxed">
                            {{ $moment['description'] }}
                        </p>
                    </div>

                    {{-- Bottom Monospace Date & Location Metadata Tag --}}
                    <div class="pt-3 border-t border-surface-border flex items-center justify-between gap-2">
                        <span class="font-mono text-[11px] uppercase tracking-[0.05em] text-primary-500 font-semibold">
                            {{ $moment['meta'] }}
                        </span>
                        <span class="font-mono text-xs text-secondary-700 font-bold" aria-hidden="true">
                            [+]
                        </span>
                    </div>
                </article>
            @endforeach

        </div>

    </div>
</section>

