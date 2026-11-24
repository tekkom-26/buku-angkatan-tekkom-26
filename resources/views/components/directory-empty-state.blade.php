@props([
    'searchQuery' => null,
    'selectedClass' => null,
    'resetUrl' => null,
    'useLivewireReset' => false,
])

@php
    $resolvedResetUrl = $resetUrl ?? (\Illuminate\Support\Facades\Route::has('directory.index') ? route('directory.index') : '/directory');
@endphp

<div
    data-directory-empty-state
    class="mx-auto my-8 max-w-lg rounded-2xl border border-white/10 bg-surface-card/85 p-8 sm:p-10 text-center"
>
    <div class="mx-auto mb-5 size-20 rounded-2xl border border-white/10 overflow-hidden">
        <x-wireframe-slot variant="empty" />
    </div>

    <h3 class="font-display text-xl sm:text-2xl font-bold text-text-primary tracking-tight mb-2">
        Tidak ada hasil ditemukan
    </h3>
    <p class="font-body text-sm text-text-secondary leading-relaxed max-w-md mx-auto mb-6">
        Belum ada mahasiswa yang cocok dengan kata kunci atau filter kelas saat ini. Coba ubah kata kunci atau atur ulang filter pencarian.
    </p>

    <div class="flex items-center justify-center">
        @if ($useLivewireReset)
            <button
                type="button"
                wire:click="resetFilters"
                class="inline-flex items-center justify-center gap-2 min-h-11 px-5 py-2.5 rounded-xl bg-brand-red hover:bg-brand-red-hover text-text-primary font-display font-semibold text-sm border border-brand-red-hover transition-colors cursor-pointer"
            >
                <svg class="size-4 shrink-0 stroke-[2] stroke-current text-brand-gold" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span>Reset Semua Filter</span>
            </button>
        @else
            <a
                href="{{ $resolvedResetUrl }}"
                @click="$dispatch('reset-directory-filter')"
                class="inline-flex items-center justify-center gap-2 min-h-11 px-5 py-2.5 rounded-xl bg-brand-red hover:bg-brand-red-hover text-text-primary font-display font-semibold text-sm border border-brand-red-hover transition-colors"
            >
                <svg class="size-4 shrink-0 stroke-[2] stroke-current text-brand-gold" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span>Reset Semua Filter</span>
            </a>
        @endif
    </div>
</div>
