@props([
    'activePage' => 'home', // 'home' | 'identity' | 'directory' | 'student' | 'unlock' | 'gallery' | 'contact' | 'error'
    'isUnlocked' => null,
])

@php
    $unlocked = $isUnlocked ?? (
        request()->attributes->get('student_role') === 'student'
        || app(\App\Services\StudentSessionService::class)->resolveRequestRole(request()) === 'student'
    );

    $isUnlockPage = $activePage === 'unlock' || request()->routeIs('unlock');

    $isDirectoryContext = $activePage !== 'error' && ! $isUnlockPage && (
        in_array($activePage, ['directory', 'student', 'students.show'])
        || request()->routeIs('directory.*')
        || request()->routeIs('students.*')
    );
@endphp

<header
    x-data="{ mobileOpen: false }"
    @keydown.escape.window="mobileOpen = false"
    class="sticky top-4 z-50 w-full px-4 sm:px-6 lg:px-8 pt-2"
>
    <div class="max-w-6xl mx-auto">
        {{-- Floating Pill Navigation Bar (Dark Frosted Glass + Hairline Border) --}}
        <div class="flex items-center justify-between h-14 sm:h-16 px-4 sm:px-6 rounded-full bg-surface-card/85 backdrop-blur-md border border-white/10 shadow-sm transition-colors">

            {{-- Left Block: Brand Wordmark --}}
            <div class="shrink-0 flex items-center">
                <a
                    href="{{ route('landing') }}"
                    class="inline-flex items-center gap-3 group rounded-full py-1 pr-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-gold focus:outline-none"
                    aria-label="Beranda COMETS 2026 — Teknik Komputer"
                >
                    <div
                        class="size-9 rounded-lg border border-brand-gold/40 bg-surface-elevated flex items-center justify-center font-mono text-xs text-brand-gold font-bold shrink-0 group-hover:border-brand-gold transition-colors"
                        aria-hidden="true"
                    >
                        C26
                    </div>

                    <span class="font-display text-sm sm:text-base font-bold tracking-tight text-text-primary group-hover:text-brand-gold transition-colors leading-none">
                        COMETS 2026
                    </span>
                </a>
            </div>

            {{-- Center Block: Primary Navigation Links --}}
            <nav aria-label="Navigasi Utama" class="hidden md:flex items-center justify-center gap-1 lg:gap-2 text-sm">
                <a
                    href="{{ route('landing') }}"
                    aria-label="Beranda"
                    class="px-3.5 py-2 rounded-full font-display text-xs lg:text-sm transition-colors {{ $activePage === 'home' ? 'text-brand-gold bg-brand-gold/10 border border-brand-gold/30 font-semibold' : 'text-text-secondary hover:text-text-primary font-medium' }}"
                >
                    Beranda
                </a>
                <a
                    href="{{ route('identity') }}"
                    aria-label="Filosofi dan Identitas"
                    class="px-3.5 py-2 rounded-full font-display text-xs lg:text-sm transition-colors {{ $activePage === 'identity' ? 'text-brand-gold bg-brand-gold/10 border border-brand-gold/30 font-semibold' : 'text-text-secondary hover:text-text-primary font-medium' }}"
                >
                    Identitas
                </a>
                <a
                    href="{{ route('directory.index') }}"
                    aria-label="Direktori Mahasiswa"
                    class="px-3.5 py-2 rounded-full font-display text-xs lg:text-sm transition-colors {{ $isDirectoryContext ? 'text-brand-gold bg-brand-gold/10 border border-brand-gold/30 font-semibold' : 'text-text-secondary hover:text-text-primary font-medium' }}"
                >
                    Direktori
                </a>
                <a
                    href="{{ route('landing') }}#moments-section"
                    aria-label="Galeri Momen Angkatan"
                    class="px-3.5 py-2 rounded-full font-display text-xs lg:text-sm transition-colors {{ $activePage === 'gallery' ? 'text-brand-gold bg-brand-gold/10 border border-brand-gold/30 font-semibold' : 'text-text-secondary hover:text-text-primary font-medium' }}"
                >
                    Galeri
                </a>
            </nav>

            {{-- Right Block: Session Action Button --}}
            <div class="hidden md:flex shrink-0 items-center justify-end gap-2.5">
                @if($isDirectoryContext || $isUnlockPage)
                    <a
                        href="{{ route('landing') }}"
                        class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-full font-display text-xs font-medium text-text-secondary hover:text-text-primary bg-surface-elevated hover:border-brand-gold/40 border border-white/10 transition-colors"
                    >
                        <span aria-hidden="true">&larr;</span>
                        <span>Beranda</span>
                    </a>
                @endif

                @if($unlocked)
                    <form method="POST" action="{{ route('lock') }}" class="inline-flex">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary font-display text-xs lg:text-sm font-semibold transition-colors cursor-pointer"
                            title="Kunci Kembali / Keluar Sesi"
                            aria-label="Keluar dan kunci kembali direktori"
                        >
                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" y1="12" x2="9" y2="12" />
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                @elseif(!$isUnlockPage)
                    <a
                        href="{{ route('unlock') }}"
                        aria-label="Masuk untuk akses direktori"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary font-display text-xs lg:text-sm font-semibold transition-colors"
                    >
                        <svg class="size-3.5 text-brand-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <span>Masuk</span>
                    </a>
                @endif
            </div>

            {{-- Mobile Menu Toggle (44x44px Touch Target) --}}
            <div class="flex md:hidden items-center gap-2">
                <button
                    type="button"
                    @click="mobileOpen = !mobileOpen"
                    class="size-11 flex items-center justify-center text-text-primary hover:text-brand-gold bg-surface-elevated border border-white/10 rounded-full transition-colors cursor-pointer"
                    :aria-expanded="mobileOpen.toString()"
                    aria-controls="mobile-navigation-drawer"
                    aria-label="Toggle menu navigasi"
                >
                    <svg x-show="!mobileOpen" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="4" y1="6" x2="20" y2="6"></line>
                        <line x1="4" y1="12" x2="20" y2="12"></line>
                        <line x1="4" y1="18" x2="20" y2="18"></line>
                    </svg>
                    <svg x-cloak x-show="mobileOpen" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Navigation Drawer --}}
        <div
            id="mobile-navigation-drawer"
            x-cloak
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            @click.outside="mobileOpen = false"
            class="md:hidden mt-2 rounded-2xl border border-white/10 bg-surface-card/95 backdrop-blur-md p-4 space-y-3 shadow-lg"
        >
            <div class="flex flex-col space-y-1 text-sm font-display">
                <a
                    href="{{ route('landing') }}"
                    class="min-h-11 flex items-center px-3.5 py-2.5 rounded-lg transition-colors {{ $activePage === 'home' ? 'text-brand-gold font-semibold bg-brand-gold/10 border border-brand-gold/30' : 'text-text-secondary hover:text-text-primary hover:bg-surface-elevated font-medium' }}"
                >
                    Beranda
                </a>
                <a
                    href="{{ route('identity') }}"
                    class="min-h-11 flex items-center px-3.5 py-2.5 rounded-lg transition-colors {{ $activePage === 'identity' ? 'text-brand-gold font-semibold bg-brand-gold/10 border border-brand-gold/30' : 'text-text-secondary hover:text-text-primary hover:bg-surface-elevated font-medium' }}"
                >
                    Filosofi &amp; Identitas
                </a>
                <a
                    href="{{ route('directory.index') }}"
                    class="min-h-11 flex items-center px-3.5 py-2.5 rounded-lg transition-colors {{ $isDirectoryContext ? 'text-brand-gold font-semibold bg-brand-gold/10 border border-brand-gold/30' : 'text-text-secondary hover:text-text-primary hover:bg-surface-elevated font-medium' }}"
                >
                    Direktori Mahasiswa
                </a>
                <a
                    href="{{ route('landing') }}#moments-section"
                    @click="mobileOpen = false"
                    class="min-h-11 flex items-center px-3.5 py-2.5 rounded-lg transition-colors {{ $activePage === 'gallery' ? 'text-brand-gold font-semibold bg-brand-gold/10 border border-brand-gold/30' : 'text-text-secondary hover:text-text-primary hover:bg-surface-elevated font-medium' }}"
                >
                    Galeri Kegiatan
                </a>
            </div>

            @if($unlocked || !$isUnlockPage)
                <div class="pt-3 border-t border-white/10 flex items-center justify-between gap-2">
                    @if($unlocked)
                        <form method="POST" action="{{ route('lock') }}" class="w-full">
                            @csrf
                            <button
                                type="submit"
                                class="w-full min-h-11 py-2.5 px-4 rounded-lg bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary font-display font-semibold text-sm flex items-center justify-center gap-2 transition-colors cursor-pointer"
                            >
                                <span>Logout / Kunci Sesi</span>
                            </button>
                        </form>
                    @else
                        <a
                            href="{{ route('unlock') }}"
                            class="w-full min-h-11 py-2.5 px-4 rounded-lg bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary font-display font-semibold text-sm flex items-center justify-center gap-2 transition-colors"
                        >
                            <span>Masuk ke Direktori</span>
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</header>
