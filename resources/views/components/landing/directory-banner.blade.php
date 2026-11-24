@props([
    'content' => null,
    'headline' => null,
    'description' => null,
    'directoryUrl' => null,
    'totalStudents' => null,
])

@php
    $c = $content ?? [];
    $rawHeadline = $c['headline'] ?? $headline ?? 'Temukan Rekan Satu Angkatan di Direktori Resmi';
    $displayHeadline = ($rawHeadline === 'Temukan Rekan Satu Angkatan di Direktori Resmi')
        ? 'Member Angkatan 2026'
        : $rawHeadline;
    $description = $c['description'] ?? $description ?? 'Akses profil lengkap mahasiswa Teknik Komputer 2026 berdasarkan kelas, domisili, dan minat eksplorasi melalui sistem direktori terverifikasi.';
    $rawDirectoryUrl = $c['directory_url'] ?? $directoryUrl ?? null;

    $resolvedDirectoryUrl = $rawDirectoryUrl ?? (
        \Illuminate\Support\Facades\Route::has('directory.index')
            ? route('directory.index')
            : '/directory'
    );
@endphp

<section id="directory-banner-section" aria-labelledby="directory-banner-heading" class="relative w-full bg-ambient-matrix py-14 sm:py-20 lg:py-24 border-t border-white/10 overflow-hidden">
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="rounded-2xl border border-white/10 bg-surface-card/75 backdrop-blur-xs p-6 sm:p-10 lg:p-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">

                {{-- Left: Editorial Copy & CTA --}}
                <div class="lg:col-span-6 space-y-4">
                    <p class="font-display text-xs font-semibold uppercase tracking-widest text-brand-gold">
                        Kelas A · Kelas B · Kelas C · Kelas D
                    </p>

                    <h2
                        id="directory-banner-heading"
                        class="font-display text-3xl sm:text-4xl font-bold text-text-primary tracking-tight leading-tight"
                    >
                        {{ $displayHeadline }}
                    </h2>

                    <p class="font-body text-sm sm:text-base text-text-secondary leading-relaxed max-w-xl">
                        {{ $description }}
                    </p>

                    <div class="pt-2">
                        <a
                            href="{{ $resolvedDirectoryUrl }}"
                            class="inline-flex items-center justify-center gap-2 min-h-11 px-6 py-3 border border-brand-gold/50 bg-brand-gold/10 text-brand-gold hover:bg-brand-gold/20 font-display font-semibold text-sm sm:text-base rounded-xl transition-colors"
                        >
                            <span>Buka Direktori</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>

                {{-- Right: Open Typographic Stats Row (No Nested Cookie-Cutter Boxes) --}}
                <div class="lg:col-span-6 grid grid-cols-3 divide-x divide-white/10 border-t lg:border-t-0 lg:border-l border-white/10 pt-8 lg:pt-0 lg:pl-8">
                    <div class="pr-4 sm:px-5">
                        <div class="font-display text-3xl sm:text-5xl font-bold text-brand-gold tracking-tight">
                            205
                        </div>
                        <p class="mt-2 font-body text-xs sm:text-sm text-text-secondary font-medium">
                            Mahasiswa Aktif
                        </p>
                    </div>

                    <div class="px-4 sm:px-5">
                        <div class="font-display text-3xl sm:text-5xl font-bold text-brand-gold tracking-tight">
                            4
                        </div>
                        <p class="mt-2 font-body text-xs sm:text-sm text-text-secondary font-medium">
                            Kelas
                        </p>
                    </div>

                    <div class="pl-4 sm:px-5">
                        <div class="font-display text-3xl sm:text-5xl font-bold text-brand-gold tracking-tight">
                            2026
                        </div>
                        <p class="mt-2 font-body text-xs sm:text-sm text-text-secondary font-medium">
                            Angkatan
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section>
