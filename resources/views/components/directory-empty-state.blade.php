@props([
    'searchQuery' => null,
    'selectedClass' => null,
    'resetUrl' => null,
])

@php
    $resolvedResetUrl = $resetUrl ?? (\Illuminate\Support\Facades\Route::has('directory.index') ? route('directory.index') : '/directory');
@endphp

{{-- ==========================================================================
     STUDENT DIRECTORY EMPTY STATE COMPONENT (BEE-NER MASCOT THEME — COMETS 2026)
     Mengacu pada .revamp-ui/PRD.md §3.3.3 & DESIGN_TOKENS.md
     ========================================================================== --}}
<div
    data-directory-empty-state
    class="mx-auto my-8 max-w-xl rounded-xl border border-brand-red-hover/60 bg-surface-card/90 p-8 sm:p-12 text-center relative overflow-hidden glow-red-subtle"
>
    {{-- Top Dual-Tone Circuit Line --}}
    <div class="absolute inset-x-0 top-0 h-1 flex" aria-hidden="true">
        <div class="w-1/2 bg-brand-red"></div>
        <div class="w-1/2 bg-brand-gold"></div>
    </div>

    {{-- Top Technical Status Tag --}}
    <div class="mb-5">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-brand-red border border-brand-red-hover text-text-primary font-mono text-[10px] font-bold uppercase tracking-wider">
            <span class="size-1.5 rounded-full bg-brand-gold animate-pulse" aria-hidden="true"></span>
            <span>[QUERY_RESULT: 0_MATCHES_FOUND]</span>
        </span>
    </div>

    {{-- Bee-ner Mascot Placeholder & User Not Found Wireframe Box --}}
    <div class="mx-auto mb-4 flex size-24 flex-col items-center justify-center rounded-xl border border-brand-gold/30 bg-canvas-base/80 p-3 glow-gold-subtle">
        <span class="sr-only">[X] [ICON: USER_NOT_FOUND]</span>
        <img
            src="{{ asset('/assets/Placeholder.png') }}"
            alt="[X: Bee-ner Mascot 2D Vector]"
            class="w-full h-full object-contain opacity-85"
            loading="lazy"
        />
    </div>
    <p class="font-mono text-[10px] uppercase tracking-wider text-brand-gold mb-3">
        [X: Bee-ner Mascot 2D Vector] // [X] [ICON: USER_NOT_FOUND]
    </p>

    {{-- Editorial Empty State Title & Message --}}
    <h3 class="font-display text-xl sm:text-2xl font-bold text-text-primary tracking-tight mb-2">
        Tidak ada hasil ditemukan
    </h3>
    <p class="font-body text-sm sm:text-base text-text-secondary leading-relaxed max-w-md mx-auto mb-6">
        Simpul mahasiswa yang kamu cari belum ditemukan pada kombinasi kata kunci atau filter kelas saat ini. Silakan periksa kembali ejaan nama/NIM atau atur ulang filter pencarian.
    </p>

    {{-- Reset Filter Action Button --}}
    <div class="flex items-center justify-center">
        <a
            href="{{ $resolvedResetUrl }}"
            @click="$dispatch('reset-directory-filter')"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-brand-red hover:bg-brand-red-hover text-text-primary font-display font-semibold text-sm border border-brand-red-hover transition-colors glow-red-subtle"
        >
            <svg class="size-4 shrink-0 stroke-[2] stroke-current text-brand-gold" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            <span>Reset Semua Filter</span>
        </a>
    </div>
</div>
