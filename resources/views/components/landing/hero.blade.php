@props([
    'content' => null,
    'title' => null,
    'motto' => null,
    'narrative' => null,
    'directoryUrl' => null,
    'identityUrl' => null,
])

@php
    $c = $content ?? [];
    $rawTitle = $c['title'] ?? $title ?? 'ANGKATAN // TEKKOM 2026';
    $displayTitle = ($rawTitle === 'ANGKATAN // TEKKOM 2026') ? 'COMETS 2026' : $rawTitle;
    $subtitle = $c['subtitle'] ?? 'Computer Engineering Twenty Six — Universitas Diponegoro';
    $motto = $c['motto'] ?? $motto ?? null;
    $narrative = $c['narrative'] ?? $narrative ?? 'Arsip digital resmi mahasiswa Teknik Komputer angkatan 2026 Fakultas Teknik Universitas Diponegoro.';
    $directoryUrl = $c['directory_url'] ?? $directoryUrl ?? route('directory.index');
    $identityUrl = $c['identity_url'] ?? $identityUrl ?? route('identity');
@endphp

<section id="hero-section" aria-label="Hero" class="relative w-full bg-ambient-hero pt-10 pb-14 sm:pt-16 sm:pb-20 overflow-hidden">
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Clean Editorial Hero Header --}}
        <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-12">
            <p class="font-display text-xs sm:text-sm font-semibold uppercase tracking-widest text-brand-gold mb-3">
                Teknik Komputer · FT Undip · 2026
            </p>

            <h1 class="font-display text-4xl sm:text-6xl lg:text-7xl font-bold text-text-primary tracking-tight leading-none mb-4">
                {{ $displayTitle }}
            </h1>

            <p class="font-body text-sm sm:text-base text-text-secondary max-w-xl mx-auto leading-relaxed">
                {{ ($motto && $motto !== 'LOREM IPSUM DOLOR!!!') ? $motto : $subtitle }}
            </p>
        </div>

        {{-- Moments Media Carousel --}}
        <div class="mb-8 sm:mb-10">
            <x-landing.moments />
        </div>

        {{-- Narrative Summary --}}
        <div class="max-w-2xl mx-auto text-center">
            <p class="font-body text-sm sm:text-base text-text-secondary leading-relaxed">
                {{ $narrative }}
            </p>

            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <a
                    href="{{ $directoryUrl }}"
                    class="inline-flex items-center justify-center gap-2 min-h-11 px-5 py-2.5 rounded-xl bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary font-display text-sm font-semibold transition-colors"
                >
                    <span>Jelajahi Direktori</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
                <a
                    href="{{ $identityUrl }}"
                    class="inline-flex items-center justify-center gap-2 min-h-11 px-5 py-2.5 rounded-xl border border-white/15 hover:border-brand-gold/50 bg-surface-card/70 text-text-primary hover:text-brand-gold font-display text-sm font-medium transition-colors"
                >
                    <span>Tentang Identitas</span>
                </a>
            </div>
        </div>

    </div>
</section>
