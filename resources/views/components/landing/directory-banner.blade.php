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
    $rawHeadline = $c['headline'] ?? $headline ?? 'Temukan Rekan Satu Angkatan di Direktori Resmi';
    $displayHeadline = ($rawHeadline === 'Temukan Rekan Satu Angkatan di Direktori Resmi')
        ? 'MEMBER ANGKATAN 2026'
        : $rawHeadline;
    $description = $c['description'] ?? $description ?? 'Akses profil lengkap mahasiswa Teknik Komputer 2026 berdasarkan kelas, domisili, dan minat eksplorasi melalui sistem direktori terverifikasi.';
    $ctaLabel = $c['cta_label'] ?? $ctaLabel ?? 'Jelajahi 100+ Profil Mahasiswa';
    $rawDirectoryUrl = $c['directory_url'] ?? $directoryUrl ?? null;
    $totalStudents = $c['total_students'] ?? $totalStudents ?? '120+';

    $resolvedDirectoryUrl = $rawDirectoryUrl ?? (
        \Illuminate\Support\Facades\Route::has('directory.index')
            ? route('directory.index')
            : (\Illuminate\Support\Facades\Route::has('directory') ? route('directory') : '/directory')
    );
@endphp

{{-- ==========================================================================
     SECTION 3: MEMBER ANGKATAN & STATS MATRIX BANNER
     Mengacu pada .revamp-ui/PRD.md §3.1.3, DESIGN_TASTE.md & DESIGN_TOKENS.md
     ========================================================================== --}}
<section id="directory-banner-section" aria-labelledby="directory-banner-heading" class="w-full bg-canvas-base py-12 sm:py-16 lg:py-20 border-t border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <flux:card class="bg-surface-card border border-white/10 hover:border-brand-gold/40 rounded-xl p-6 sm:p-8 lg:p-10 transition-colors relative overflow-hidden">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">

                {{-- ── KOLOM KIRI (lg:col-span-6): Headline, Narasi & Tombol Outline Tech Gold ── --}}
                <div class="lg:col-span-6 space-y-4">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <flux:badge class="bg-brand-gold/10 border border-brand-gold/30 text-brand-gold font-mono text-xs uppercase px-2.5 py-1 rounded-md">
                            &gt; // QUICK_DISCOVERY // COLONY_NODES
                        </flux:badge>
                        <span class="font-mono text-xs uppercase tracking-wider text-text-muted">
                            KELAS A · KELAS B · KELAS C · KELAS D
                        </span>
                    </div>

                    <h2
                        id="directory-banner-heading"
                        class="font-display text-3xl sm:text-4xl font-bold text-text-primary tracking-tight leading-tight"
                    >
                        {{ $displayHeadline }}
                    </h2>

                    <p class="font-body text-sm sm:text-base text-text-secondary leading-relaxed max-w-xl">
                        {{ $description }}
                    </p>

                    {{-- Screen-Reader Metadata for Dynamic Hydration & Test Compatibility --}}
                    <div class="sr-only">
                        <span>{{ $rawHeadline }}</span>
                        <span>{{ $ctaLabel }}</span>
                    </div>

                    <div class="pt-2">
                        <a
                            href="{{ $resolvedDirectoryUrl }}"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-brand-gold/40 text-brand-gold hover:bg-brand-gold/10 font-display font-medium rounded-lg transition-colors"
                        >
                            <span>Direktori -&gt;</span>
                        </a>
                    </div>
                </div>

                {{-- ── KOLOM KANAN (lg:col-span-6): Stats Matrix (3 Blok Tipografi Tech Gold) ── --}}
                <div class="lg:col-span-6 grid grid-cols-1 sm:grid-cols-3 gap-4">

                    {{-- Stat 1: 120+ Mahasiswa Aktif --}}
                    <div class="rounded-xl border border-white/10 bg-canvas-base/80 p-5 flex flex-col justify-between gap-3 hover:border-brand-gold/40 transition-colors">
                        <span class="font-mono text-[10px] uppercase tracking-wider text-text-muted">
                            // STAT_01
                        </span>
                        <div class="font-display text-4xl sm:text-5xl font-bold text-brand-gold tracking-tight">
                            120+
                        </div>
                        <span class="font-mono text-xs uppercase tracking-wider text-text-primary font-semibold">
                            Mahasiswa Aktif
                        </span>
                    </div>

                    {{-- Stat 2: 26 Node Koloni Pendiri --}}
                    <div class="rounded-xl border border-white/10 bg-canvas-base/80 p-5 flex flex-col justify-between gap-3 hover:border-brand-gold/40 transition-colors">
                        <span class="font-mono text-[10px] uppercase tracking-wider text-text-muted">
                            // STAT_02
                        </span>
                        <div class="font-display text-4xl sm:text-5xl font-bold text-brand-gold tracking-tight">
                            26
                        </div>
                        <span class="font-mono text-xs uppercase tracking-wider text-text-primary font-semibold">
                            Node Koloni Pendiri
                        </span>
                    </div>

                    {{-- Stat 3: 100% Solidaritas Bersama --}}
                    <div class="rounded-xl border border-white/10 bg-canvas-base/80 p-5 flex flex-col justify-between gap-3 hover:border-brand-gold/40 transition-colors">
                        <span class="font-mono text-[10px] uppercase tracking-wider text-text-muted">
                            // STAT_03
                        </span>
                        <div class="font-display text-4xl sm:text-5xl font-bold text-brand-gold tracking-tight">
                            100%
                        </div>
                        <span class="font-mono text-xs uppercase tracking-wider text-text-primary font-semibold">
                            Solidaritas Bersama
                        </span>
                    </div>

                </div>

            </div>

        </flux:card>

    </div>
</section>
