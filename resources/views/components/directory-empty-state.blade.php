@props([
    'searchQuery' => null,
    'selectedClass' => null,
    'resetUrl' => null,
])

@php
    $resolvedResetUrl = $resetUrl ?? (\Illuminate\Support\Facades\Route::has('directory.index') ? route('directory.index') : '/directory');
@endphp

{{-- ==========================================================================
     STUDENT DIRECTORY EMPTY STATE COMPONENT
     Mengacu pada GitHub Issue #14, DESIGN.md, dan DOCS_COLOR_CONVENTION.md
     Figma Reference: Node 34:1067 / 34:1068 (Centered Empty State) & 34:1095 (64x64 Icon)
     ========================================================================== --}}
<div
    data-directory-empty-state
    class="mx-auto my-8 max-w-xl rounded-2xl border border-surface-border bg-surface-card p-8 sm:p-12 text-center shadow-xs relative overflow-hidden"
>
    {{-- Blueprint Corner Crosshairs --}}
    <span class="absolute top-3 left-4 font-mono text-xs text-primary-500/50 select-none pointer-events-none" aria-hidden="true">+</span>
    <span class="absolute top-3 right-4 font-mono text-xs text-primary-500/50 select-none pointer-events-none" aria-hidden="true">+</span>
    <span class="absolute bottom-3 left-4 font-mono text-xs text-primary-500/50 select-none pointer-events-none" aria-hidden="true">+</span>
    <span class="absolute bottom-3 right-4 font-mono text-xs text-primary-500/50 select-none pointer-events-none" aria-hidden="true">+</span>

    {{-- Top Technical Status Tag --}}
    <div class="mb-5">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-highlight-500 text-highlight-900 font-mono text-[10px] font-bold uppercase tracking-[0.05em]">
            [QUERY_RESULT: 0_MATCHES_FOUND]
        </span>
    </div>

    {{-- 64x64px Wireframe Box User Not Found Icon (Figma Node 34:1095) --}}
    <div class="mx-auto mb-5 flex size-16 flex-col items-center justify-center rounded-xl border border-surface-border bg-surface-canvas p-2 shadow-2xs">
        <span class="sr-only">[X] [ICON: USER_NOT_FOUND]</span>
        <svg class="size-8 stroke-[1.75] stroke-current text-primary-700" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="m18 14 3 3m0-3-3 3" />
        </svg>
    </div>
    <p class="font-mono text-[10px] uppercase tracking-[0.05em] text-primary-500 mb-3">
        [X] [ICON: USER_NOT_FOUND]
    </p>

    {{-- Editorial Empty State Title & Message --}}
    <h3 class="text-xl sm:text-2xl font-bold text-primary-900 tracking-[-0.02em] mb-2">
        Tidak ada hasil ditemukan
    </h3>
    <p class="text-sm sm:text-[15px] text-primary-900/80 leading-relaxed max-w-md mx-auto mb-6">
        Data mahasiswa yang kamu cari belum ditemukan pada kombinasi kata kunci atau filter kelas saat ini. Silakan periksa kembali ejaan nama/NIM atau atur ulang filter pencarian.
    </p>

    {{-- Reset Filter Action Button --}}
    <div class="flex items-center justify-center">
        <a
            href="{{ $resolvedResetUrl }}"
            @click="$dispatch('reset-directory-filter')"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-secondary-500 text-primary-900 font-semibold text-sm border border-surface-border shadow-xs hover:bg-secondary-700 hover:text-white active:scale-[0.98] transition-all duration-180 focus-visible:outline-2 focus-visible:outline-secondary-500 focus-visible:outline-offset-2"
        >
            <svg class="size-4 shrink-0 stroke-[2] stroke-current" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            <span>Reset Semua Filter</span>
        </a>
    </div>
</div>
