@props([
    'content' => null,
    'title' => null,
    'subhead' => null,
    'bannerPlaceholder' => null,
])

@php
    $c = $content ?? [];
    $rawTitle = $c['title'] ?? $title ?? 'Identitas, Filosofi & Nilai Dasar';
    $displayTitle = ($rawTitle === 'Identitas, Filosofi & Nilai Dasar')
        ? 'Anatomi Identitas COMETS'
        : $rawTitle;
    $subhead = $c['subhead'] ?? $subhead ?? 'Dokumentasi mengenai filosofi koloni lebah biner COMETS (Computer Engineering Twenty Six), visi dan misi, lima elemen lambang, anatomi maskot Bee-ner, serta tiga warna resmi angkatan Teknik Komputer 2026.';
    $bannerPlaceholder = $c['banner_placeholder'] ?? $bannerPlaceholder ?? '/assets/Placeholder.png';
    $isPlaceholderBanner = empty($bannerPlaceholder) || str_contains($bannerPlaceholder, 'Placeholder.png');
@endphp

<section
    id="identity-header-section"
    aria-labelledby="identity-page-heading"
    {{ $attributes->merge(['class' => 'relative w-full bg-ambient-hero text-text-primary pt-10 pb-14 sm:pt-14 sm:pb-18 overflow-hidden']) }}
>
    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <header class="mb-8 sm:mb-10 max-w-3xl">
            <p class="font-display text-xs sm:text-sm font-semibold uppercase tracking-widest text-brand-gold mb-3">
                Filosofi &amp; Simbolisme &middot; {{ $cohortName ?? 'TEKKOM 2026' }}
            </p>

            <h1
                id="identity-page-heading"
                class="font-display text-3xl sm:text-5xl lg:text-6xl font-bold text-text-primary tracking-tight leading-tight"
            >
                {{ $displayTitle }}
            </h1>

            <p class="mt-4 font-body text-sm sm:text-base leading-relaxed text-text-secondary">
                {{ $subhead }}
            </p>
        </header>

        {{-- Clean 21:9 Cohort Banner Frame --}}
        <div class="relative w-full aspect-video sm:aspect-[21/9] rounded-2xl border border-white/10 bg-surface-card/70 overflow-hidden">
            @if ($isPlaceholderBanner)
                <x-wireframe-slot
                    variant="banner"
                    label="Foto Kolaboratif Angkatan"
                    hint="Teknik Komputer FT Undip 2026"
                />
            @else
                <img
                    src="{{ asset($bannerPlaceholder) }}"
                    alt="Banner Kolaboratif Angkatan Teknik Komputer 2026"
                    class="h-full w-full object-cover select-none"
                    loading="lazy"
                />
            @endif
        </div>

    </div>
</section>
