@props([
    'moments' => [
        [
            'id' => 'moment-01',
            'categoryLabel' => 'Prestasi',
            'image' => '/assets/Placeholder.png',
            'title' => 'Inaugurasi & Pengukuhan Mahasiswa Baru Teknik Komputer 2026',
            'description' => 'Momentum awal kebersamaan seluruh anggota angkatan Teknik Komputer 2026 di lingkungan kampus Tembalang.',
            'meta' => '24 Okt 2026',
        ],
        [
            'id' => 'moment-02',
            'categoryLabel' => 'Kegiatan Lab',
            'image' => '/assets/Placeholder.png',
            'title' => 'Eksplorasi Praktikum Sistem Digital & Arsitektur Perangkat Keras',
            'description' => 'Kolaborasi pemecahan masalah teknis dan perakitan modul praktikum bersama di laboratorium.',
            'meta' => '12 Nov 2026',
        ],
        [
            'id' => 'moment-03',
            'categoryLabel' => 'Kebersamaan',
            'image' => '/assets/Placeholder.png',
            'title' => 'Malam Keakraban & Forum Solidaritas Keluarga Besar COMETS 2026',
            'description' => 'Menyatukan kebersamaan lintas kelas dalam ruang silaturahmi angkatan Teknik Komputer 2026.',
            'meta' => '05 Des 2026',
        ],
    ],
])

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
    <h2 id="moments-section-heading" class="sr-only">Dokumentasi &amp; Kilas Balik Angkatan</h2>

    <div class="rounded-2xl border border-white/10 bg-surface-card/70 backdrop-blur-xs overflow-hidden">
        {{-- Media Stage (Arrows positioned safely inside media area only) --}}
        <div class="relative w-full aspect-video sm:aspect-[21/9] bg-canvas-base overflow-hidden">
            @foreach ($moments as $index => $moment)
                @php
                    $isPlaceholder = empty($moment['image']) || str_contains($moment['image'], 'Placeholder.png');
                @endphp
                <div
                    x-show="activeSlide === {{ $index }}"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0"
                >
                    @if ($isPlaceholder)
                        <x-wireframe-slot
                            variant="moment"
                            :label="'Dokumentasi: ' . $moment['categoryLabel']"
                            :hint="$moment['meta']"
                        />
                    @else
                        <img
                            src="{{ asset($moment['image']) }}"
                            alt="{{ $moment['title'] }}"
                            class="w-full h-full object-cover select-none"
                            loading="lazy"
                        />
                        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-canvas-base/90 via-canvas-base/30 to-transparent hidden sm:block"></div>
                    @endif

                    {{-- Category Pill --}}
                    <div class="absolute top-3.5 left-3.5 sm:top-5 sm:left-5 z-10">
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-surface-card/90 backdrop-blur-xs border border-white/15 font-display text-xs font-medium text-brand-gold">
                            {{ $moment['categoryLabel'] }}
                        </span>
                    </div>

                    {{-- Desktop Overlay Caption (Never overlaps arrows thanks to generous px-16) --}}
                    <div class="hidden sm:block absolute inset-x-0 bottom-0 z-10 px-16 pb-5 pt-8 text-left pointer-events-none">
                        <div class="flex items-end justify-between gap-4">
                            <div class="max-w-2xl">
                                <h3 class="font-display text-base lg:text-lg font-bold text-text-primary leading-snug">
                                    {{ $moment['title'] }}
                                </h3>
                                <p class="font-body text-xs lg:text-sm text-text-secondary line-clamp-1 mt-0.5">
                                    {{ $moment['description'] }}
                                </p>
                            </div>
                            <span class="font-mono text-xs text-text-muted shrink-0">
                                {{ $moment['meta'] }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Prev / Next Controls --}}
            <button
                type="button"
                @click="prevSlide()"
                aria-label="Slide momen sebelumnya"
                class="absolute left-3 top-1/2 -translate-y-1/2 z-20 size-10 sm:size-11 rounded-full bg-surface-card/85 hover:bg-brand-red border border-white/15 hover:border-brand-red-hover text-text-primary flex items-center justify-center transition-colors cursor-pointer"
            >
                <span class="font-mono text-base leading-none" aria-hidden="true">&larr;</span>
            </button>
            <button
                type="button"
                @click="nextSlide()"
                aria-label="Slide momen berikutnya"
                class="absolute right-3 top-1/2 -translate-y-1/2 z-20 size-10 sm:size-11 rounded-full bg-surface-card/85 hover:bg-brand-red border border-white/15 hover:border-brand-red-hover text-text-primary flex items-center justify-center transition-colors cursor-pointer"
            >
                <span class="font-mono text-base leading-none" aria-hidden="true">&rarr;</span>
            </button>
        </div>

        {{-- Mobile Stacked Caption (Zero overlap with carousel arrows!) --}}
        <div class="sm:hidden border-t border-white/10 p-4 text-left">
            @foreach ($moments as $index => $moment)
                <div x-show="activeSlide === {{ $index }}">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="font-mono text-xs text-brand-gold font-medium">
                            0{{ $index + 1 }} / 0{{ count($moments) }}
                        </span>
                        <span class="font-mono text-xs text-text-muted">
                            {{ $moment['meta'] }}
                        </span>
                    </div>
                    <h3 class="font-display text-base font-bold text-text-primary leading-snug">
                        {{ $moment['title'] }}
                    </h3>
                    <p class="font-body text-xs text-text-secondary mt-1 leading-relaxed">
                        {{ $moment['description'] }}
                    </p>
                </div>
            @endforeach
        </div>

        {{-- Minimalist Pagination Bar (>= 28x28px touch target per dot) --}}
        <div class="flex items-center justify-between gap-4 px-4 sm:px-6 py-2.5 border-t border-white/10 bg-surface-card/50">
            <span class="hidden sm:inline font-mono text-xs text-text-muted whitespace-nowrap">
                <span class="text-brand-gold font-semibold" x-text="String(activeSlide + 1).padStart(2, '0')">01</span>
                / {{ str_pad((string) count($moments), 2, '0', STR_PAD_LEFT) }}
            </span>

            <div class="flex items-center gap-1 mx-auto sm:mx-0" role="tablist" aria-label="Navigasi slide momen angkatan">
                @foreach ($moments as $index => $moment)
                    <button
                        type="button"
                        role="tab"
                        @click="goToSlide({{ $index }})"
                        :aria-selected="activeSlide === {{ $index }} ? 'true' : 'false'"
                        :aria-label="'Tampilkan momen {{ $index + 1 }}'"
                        class="min-w-7 min-h-7 flex items-center justify-center cursor-pointer"
                    >
                        <span
                            :class="activeSlide === {{ $index }} ? 'w-6 bg-brand-gold' : 'w-2 bg-white/25 hover:bg-white/50'"
                            class="h-2 rounded-full transition-all duration-200 block"
                        ></span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>
