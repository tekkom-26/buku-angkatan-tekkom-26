@props([
    'content' => null,
    'headline' => null,
    'description' => null,
    'ctaLabel' => null,
    'directoryUrl' => null,
    'totalStudents' => null,
])

@php
    $c = $content ?? [];
    $headline = $c['headline'] ?? $headline ?? 'Temukan Rekan Satu Angkatan di Direktori Resmi';
    $description = $c['description'] ?? $description ?? 'Akses profil lengkap mahasiswa Teknik Komputer 2026 berdasarkan kelas, domisili, dan minat eksplorasi melalui sistem direktori terverifikasi.';
    $ctaLabel = $c['cta_label'] ?? $ctaLabel ?? 'Jelajahi 100+ Profil Mahasiswa';
    $rawDirectoryUrl = $c['directory_url'] ?? $directoryUrl ?? null;
    $totalStudents = $c['total_students'] ?? $totalStudents ?? '205';

    $resolvedDirectoryUrl = $rawDirectoryUrl ?? (
        \Illuminate\Support\Facades\Route::has('directory.index')
            ? route('directory.index')
            : (\Illuminate\Support\Facades\Route::has('directory') ? route('directory') : '/directory')
    );
@endphp

{{-- ==========================================================================
     SECTION 4: DIRECTORY QUICK DISCOVERY BANNER PLATE
     Mengacu pada GitHub Issue #21 & #12, DESIGN.md, dan DOCS_COLOR_CONVENTION.md
     Figma Reference: Node 24:740 (Directory Banner Frame)
     ========================================================================== --}}
<section id="directory-banner-section" aria-labelledby="directory-banner-heading" class="w-full bg-surface-canvas py-6 sm:py-10 pb-12 sm:pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Bento Container Plate (Snow White bg-surface-card, Hairline Border, Rounded-2xl) --}}
        <div class="relative overflow-hidden rounded-2xl border border-surface-border bg-surface-card p-6 sm:p-8 lg:p-10 shadow-xs">

            {{-- Subtle Blueprint Crosshair Accents --}}
            <span class="absolute top-3 left-4 font-mono text-xs text-primary-500/50 select-none pointer-events-none" aria-hidden="true">+</span>
            <span class="absolute top-3 right-4 font-mono text-xs text-primary-500/50 select-none pointer-events-none" aria-hidden="true">+</span>
            <span class="absolute bottom-3 left-4 font-mono text-xs text-primary-500/50 select-none pointer-events-none" aria-hidden="true">+</span>
            <span class="absolute bottom-3 right-4 font-mono text-xs text-primary-500/50 select-none pointer-events-none" aria-hidden="true">+</span>

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 lg:gap-10">

                {{-- Left Content Block: Telemetry Tag, Headline, and Description --}}
                <div class="space-y-3 max-w-2xl">

                    {{-- Top Metadata & Telemetry Badge Row --}}
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="font-mono text-[11px] sm:text-xs font-bold uppercase tracking-[0.05em] text-primary-700">
                            // QUICK_DISCOVERY // STUDENT_ACCESS
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-highlight-500 text-highlight-900 font-mono text-[10px] font-bold uppercase tracking-wider">
                            <span class="size-1.5 rounded-full bg-highlight-900" aria-hidden="true"></span>
                            [STUDENT_STATUS: {{ $totalStudents }}_VERIFIED]
                        </span>
                    </div>

                    {{-- Callout Headline (24px - 28px Bold Deep Plum) --}}
                    <h2
                        id="directory-banner-heading"
                        class="text-2xl sm:text-[28px] font-bold text-primary-900 tracking-[-0.02em] leading-tight"
                    >
                        {{ $headline }}
                    </h2>

                    {{-- Description Copy --}}
                    <p class="text-sm sm:text-[15px] text-primary-900/85 leading-relaxed">
                        {{ $description }}
                    </p>

                </div>

                {{-- Right Action Block: Primary Sky Wash CTA Button --}}
                <div class="shrink-0 flex flex-col sm:flex-row lg:flex-col items-stretch sm:items-center lg:items-end gap-3">
                    <a
                        href="{{ $resolvedDirectoryUrl }}"
                        class="inline-flex items-center justify-center gap-2.5 w-full sm:w-auto px-6 py-3.5 rounded-lg bg-secondary-500 text-primary-900 font-semibold text-sm sm:text-base border border-surface-border shadow-xs hover:brightness-105 active:scale-[0.98] transition-all duration-180 focus-visible:outline-2 focus-visible:outline-secondary-500 focus-visible:outline-offset-2"
                    >
                        <span>{{ $ctaLabel }}</span>
                        <span class="font-mono text-base leading-none" aria-hidden="true">→</span>
                    </a>
                    <span class="font-mono text-[10px] uppercase tracking-wider text-primary-500 text-center sm:text-right">
                        KELAS A · KELAS B · KELAS C · KELAS D
                    </span>
                </div>

            </div>

        </div>

    </div>
</section>

