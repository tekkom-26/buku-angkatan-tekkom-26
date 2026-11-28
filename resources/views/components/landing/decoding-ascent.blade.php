@props([
    'content' => null,
    'logoPlaceholder' => null,
    'wordmark' => null,
    'subLabel' => null,
    'movementOneHeading' => null,
    'movementOneBody' => null,
    'movementTwoHeading' => null,
    'movementTwoBody' => null,
])

@php
    $c = $content ?? [];
    $logoPlaceholder = $c['logo_placeholder'] ?? $logoPlaceholder ?? '/assets/Placeholder.png';
    $isPlaceholderImage = empty($logoPlaceholder);
    $wordmark = $c['wordmark'] ?? $wordmark ?? null;
    $jargon = $c['jargon'] ?? 'SOLID LIKE A BEE, BE LIKE WE WANT TO BE, STRONG AND FREE!';
    $movementOneHeading = $c['movement_one_heading'] ?? $movementOneHeading ?? 'Kebersamaan & Solidaritas Koloni';
    $movementOneBody = $c['movement_one_body'] ?? $movementOneBody ?? 'Layaknya satu koloni lebah yang membangun sarang heksagonal dengan presisi, setiap mahasiswa Teknik Komputer 2026 saling terhubung dan menopang tanpa ada yang tertinggal.';
    $movementTwoHeading = $c['movement_two_heading'] ?? $movementTwoHeading ?? 'Ketangguhan Melampaui Tantangan';
    $movementTwoBody = $c['movement_two_body'] ?? $movementTwoBody ?? 'Memadukan logika rekayasa perangkat keras dan lunak dengan daya tahan pantang menyerah dalam melewati masa terang maupun gelap bersama.';
@endphp

<section id="decoding-section" aria-labelledby="decoding-heading" class="relative w-full bg-ambient-colony py-14 sm:py-20 lg:py-24 border-t border-white/10 overflow-hidden">
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">

            {{-- Left Visual Showcase: Clean Uncluttered Frame --}}
            <div class="lg:col-span-5">
                <div class="aspect-square w-full max-w-[360px] mx-auto rounded-2xl border border-white/10 bg-surface-card/70 overflow-hidden">
                    @if ($isPlaceholderImage)
                        <x-wireframe-slot
                            variant="mascot"
                            label="Maskot Bee-ner & Lambang COMETS"
                            hint="Teknik Komputer 2026"
                        />
                    @else
                        <img
                            src="{{ asset($logoPlaceholder) }}"
                            alt="Lambang dan Maskot COMETS 2026"
                            class="w-full h-full object-contain p-6 select-none"
                            loading="lazy"
                        />
                    @endif
                </div>
            </div>

            {{-- Right Editorial Typography: Open & Breathing Layout --}}
            <div class="lg:col-span-7 space-y-8">
                <div>
                    <p class="font-display text-xs font-semibold uppercase tracking-widest text-brand-gold mb-3">
                        Jargon &amp; Nilai Inti{{ ($wordmark && $wordmark !== 'TEKKOM 2026') ? ' · ' . $wordmark : '' }}
                    </p>
                    <h2
                        id="decoding-heading"
                        class="font-display text-2xl sm:text-4xl lg:text-5xl font-bold text-text-primary tracking-tight leading-tight"
                    >
                        “{{ $jargon }}”
                    </h2>
                </div>

                {{-- Open Divided Pillars (No Nested Bordered Boxes / No border-t-4 Slop) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 pt-6 border-t border-white/10">
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-brand-gold">01</span>
                            <h3 class="font-display text-lg font-bold text-text-primary">
                                {{ $movementOneHeading }}
                            </h3>
                        </div>
                        <p class="font-body text-sm text-text-secondary leading-relaxed">
                            {{ $movementOneBody }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-bold text-brand-gold">02</span>
                            <h3 class="font-display text-lg font-bold text-text-primary">
                                {{ $movementTwoHeading }}
                            </h3>
                        </div>
                        <p class="font-body text-sm text-text-secondary leading-relaxed">
                            {{ $movementTwoBody }}
                        </p>
                    </div>
                </div>

                <div>
                    <a
                        href="{{ route('identity') }}"
                        class="inline-flex items-center justify-center gap-2.5 min-h-11 px-6 py-3 rounded-xl bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary font-display font-semibold text-sm sm:text-base transition-colors"
                    >
                        <span>Pelajari Filosofi &amp; Identitas</span>
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>

        </div>

    </div>
</section>
