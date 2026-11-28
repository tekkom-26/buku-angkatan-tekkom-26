@props([
    'content' => null,
    'logoPlaceholder' => '/assets/Placeholder.png',
])

@php
    $c = $content ?? [];
    $rawEyebrow = $c['eyebrow'] ?? 'Anatomi Lambang';
    $eyebrow = str_starts_with($rawEyebrow, '//') ? 'Anatomi Lambang' : $rawEyebrow;
    $title = $c['title'] ?? 'Bedah Simbolisme';
    $subtitle = $c['subtitle'] ?? '5 Elemen Lambang COMETS';
    $description = $c['description'] ?? 'Setiap lapisan geometri pada lambang COMETS 2026 dirancang dengan presisi untuk memadukan solidaritas koloni lebah, ketangguhan melewati masa terang dan gelap, serta identitas keilmuan Teknik Komputer.';

    $slides = app(\App\Services\WebContentService::class)->resolveIdentitySlides(
        $c['slides'] ?? null,
        'logo_symbolism',
    );
@endphp

<section
    id="identity-logo-section"
    aria-labelledby="logo-explorer-heading"
    {{ $attributes->merge(['class' => 'relative w-full bg-ambient-matrix text-text-primary py-14 sm:py-20 border-t border-white/10 overflow-hidden']) }}
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

        <div class="mb-8 flex flex-col gap-4 border-b border-white/10 pb-6 md:flex-row md:items-end md:justify-between">
            <div class="max-w-2xl">
                <p class="mb-2 font-display text-xs font-semibold uppercase tracking-widest text-brand-gold">
                    {{ $eyebrow }}
                </p>
                <h2 id="logo-explorer-heading" class="font-display text-2xl sm:text-4xl font-bold tracking-tight text-text-primary">
                    {{ $title }}
                    <span class="text-brand-gold">{{ $subtitle }}</span>
                </h2>
                <p class="mt-3 max-w-xl font-body text-sm sm:text-base leading-relaxed text-text-secondary">
                    {{ $description }}
                </p>
            </div>
        </div>

        {{-- Selector Tabs (>= 40px touch height) --}}
        <div class="flex flex-wrap items-center gap-2 mb-6" role="tablist" aria-label="Pilih elemen anatomi logo COMETS">
            @foreach ($slides as $slide)
                <button
                    type="button"
                    role="tab"
                    @click="goTo({{ $loop->iteration }})"
                    :aria-selected="activeSlide === {{ $loop->iteration }} ? 'true' : 'false'"
                    :class="activeSlide === {{ $loop->iteration }}
                        ? 'bg-brand-red border-brand-red-hover text-text-primary font-semibold'
                        : 'bg-surface-card/70 border-white/10 text-text-secondary hover:border-brand-gold/40 hover:text-text-primary'"
                    class="min-h-10 px-3.5 py-2 rounded-xl border font-display text-xs transition-colors cursor-pointer inline-flex items-center gap-2 whitespace-nowrap"
                >
                    <span class="font-mono text-brand-gold font-bold">{{ $slide['number'] }}</span>
                    <span>{{ $slide['part'] }}</span>
                </button>
            @endforeach
        </div>

        {{-- Split Showcase Container (Single Clean Container, Zero Double Nesting) --}}
        <div class="grid overflow-hidden rounded-2xl border border-white/10 bg-surface-card/80 lg:grid-cols-2">

            {{-- Left Visual Stage --}}
            <div class="relative min-h-[20rem] sm:min-h-[26rem] bg-canvas-base/80 border-b lg:border-b-0 lg:border-r border-white/10 grid">
                @foreach ($slides as $slide)
                    @php
                        $isSlidePlaceholder = empty($slide['image']);
                    @endphp
                    <div
                        x-show="activeSlide === {{ $loop->iteration }}"
                        x-transition:enter="transition duration-250 ease-out"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition duration-150 ease-in"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="col-start-1 row-start-1 flex items-center justify-center p-6 sm:p-10"
                    >
                        @if ($isSlidePlaceholder)
                            <x-wireframe-slot
                                variant="emblem"
                                :label="'Elemen ' . $slide['number'] . ': ' . $slide['part']"
                                :hint="$slide['detail']"
                                class="rounded-xl"
                            />
                        @else
                            <img
                                src="{{ asset($slide['image']) }}"
                                alt="{{ $slide['title'] }}"
                                class="max-h-72 max-w-full object-contain select-none"
                                loading="lazy"
                            />
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Right Editorial Dossier --}}
            <div class="flex flex-col justify-between p-6 sm:p-8 lg:p-10">
                <div>
                    <div class="mb-6 flex items-center justify-between gap-2 border-b border-white/10 pb-4">
                        <p class="font-display text-xs font-medium text-text-muted">
                            Makna Elemen
                        </p>
                        <span class="inline-flex items-center rounded-full bg-surface-elevated border border-white/10 px-3 py-1 font-mono text-xs font-semibold text-text-primary whitespace-nowrap shrink-0">
                            <span class="text-brand-gold mr-1" x-text="String(activeSlide).padStart(2, '0')">01</span> / {{ str_pad((string) count($slides), 2, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>

                    <div class="grid">
                        @foreach ($slides as $slide)
                            <div
                                x-show="activeSlide === {{ $loop->iteration }}"
                                x-transition:enter="transition duration-250 ease-out"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition duration-150 ease-in"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="col-start-1 row-start-1 min-h-48"
                            >
                                <p class="font-display text-xs font-semibold uppercase tracking-wider text-brand-gold">
                                    {{ $slide['eyebrow'] }}
                                </p>
                                <h3 class="mt-2.5 max-w-lg font-display text-2xl sm:text-3xl font-bold leading-snug tracking-tight text-text-primary">
                                    {{ $slide['title'] }}
                                </h3>
                                <p class="mt-4 max-w-lg font-body text-sm sm:text-base leading-relaxed text-text-secondary">
                                    {{ $slide['description'] }}
                                </p>
                                @if (!empty($slide['detail']))
                                    <div class="mt-6">
                                        <span class="inline-flex items-center rounded-full border border-brand-gold/30 bg-brand-gold/10 px-3.5 py-1.5 font-display text-xs font-medium text-brand-gold">
                                            {{ $slide['detail'] }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Navigation Controls (Dot touch target >= 28px, Arrows >= 44px) --}}
                <div class="mt-8 flex items-center justify-between gap-4 border-t border-white/10 pt-6">
                    <div class="flex items-center gap-1" role="tablist" aria-label="Pilih slide komponen logo">
                        @foreach ($slides as $slide)
                            <button
                                type="button"
                                class="min-w-7 min-h-7 flex items-center justify-center cursor-pointer"
                                @click="goTo({{ $loop->iteration }})"
                                :aria-label="'Tampilkan slide logo {{ $loop->iteration }}'"
                                :aria-current="activeSlide === {{ $loop->iteration }} ? 'true' : 'false'"
                            >
                                <span
                                    :class="activeSlide === {{ $loop->iteration }} ? 'w-6 bg-brand-gold' : 'w-2 bg-white/25 hover:bg-white/50'"
                                    class="h-2 rounded-full transition-all duration-200 block"
                                ></span>
                            </button>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2.5">
                        <button
                            type="button"
                            class="grid size-11 place-items-center rounded-xl border border-white/15 bg-canvas-base text-text-primary transition hover:border-brand-gold hover:text-brand-gold active:scale-95 cursor-pointer"
                            @click="previous"
                            aria-label="Slide logo sebelumnya"
                        >
                            <span class="font-mono text-lg leading-none" aria-hidden="true">&larr;</span>
                        </button>
                        <button
                            type="button"
                            class="grid size-11 place-items-center rounded-xl bg-brand-red border border-brand-red-hover text-text-primary font-semibold transition hover:bg-brand-red-hover active:scale-95 cursor-pointer"
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
