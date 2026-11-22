@props([
    'activePage' => 'home', // 'home' | 'identity' | 'directory' | 'unlock' | 'student' | 'gallery' | 'contact'
    'isUnlocked' => null,
])

@php
    $unlocked = $isUnlocked ?? (
        request()->attributes->get('student_role') === 'student'
        || app(\App\Services\StudentSessionService::class)->resolveRequestRole(request()) === 'student'
    );

    $isDirectoryContext = $activePage !== 'error' && (
        in_array($activePage, ['directory', 'student', 'students.show', 'unlock'])
        || request()->routeIs('directory.*')
        || request()->routeIs('students.*')
        || request()->routeIs('unlock')
    );
@endphp

<header
    x-data="{ mobileOpen: false }"
    @keydown.escape.window="mobileOpen = false"
    class="sticky top-4 z-50 w-full px-4 sm:px-6 lg:px-8 pt-2"
>
    <div class="max-w-6xl mx-auto">
        {{-- Floating Pill Navigation Bar (Dark Frosted Glass + 1px Hairline Border) --}}
        <div class="flex items-center justify-between h-14 sm:h-16 px-4 sm:px-6 rounded-full bg-surface-card/85 backdrop-blur-md border border-white/10 shadow-sm transition-colors">

            {{-- ── Left Block: COMETS Logo Placeholder + Brand Text ── --}}
            <div class="shrink-0 flex items-center">
                <a
                    href="{{ route('landing') }}"
                    class="inline-flex items-center gap-3 group rounded-full py-1 pr-2 focus:outline-none"
                    aria-label="Beranda COMETS 2026"
                >
                    {{-- Hexagonal / Geometric Wireframe Logo Badge --}}
                    <div
                        class="size-9 rounded-lg border border-brand-gold/40 bg-surface-elevated flex items-center justify-center font-mono text-[10px] text-brand-gold font-bold shrink-0 group-hover:border-brand-gold transition-colors"
                        title="[X: COMETS Logo]"
                    >
                        <span class="sr-only">[X: COMETS Logo]</span>
                        <span aria-hidden="true">C26</span>
                    </div>

                    <div class="flex flex-col">
                        <span class="font-display text-sm sm:text-base font-bold tracking-tight text-text-primary group-hover:text-brand-gold transition-colors leading-none">
                            COMETS 2026
                        </span>
                        <span class="sr-only">ANGKATAN // TEKKOM 2026</span>
                    </div>
                </a>
            </div>

            {{-- ── Center Block: Navigation Links (Home, Identity, Directory, Gallery) ── --}}
            <nav aria-label="Navigasi Utama" class="hidden md:flex items-center justify-center gap-1 lg:gap-2 text-sm">
                <a
                    href="{{ route('landing') }}"
                    class="px-3.5 py-1.5 rounded-full font-display text-xs lg:text-sm transition-colors {{ $activePage === 'home' ? 'text-brand-gold bg-brand-gold/10 border border-brand-gold/30 font-semibold' : 'text-text-secondary hover:text-text-primary font-medium' }}"
                >
                    <span>Home</span>
                    <span class="sr-only">Beranda</span>
                </a>
                <a
                    href="{{ route('identity') }}"
                    class="px-3.5 py-1.5 rounded-full font-display text-xs lg:text-sm transition-colors {{ $activePage === 'identity' ? 'text-brand-gold bg-brand-gold/10 border border-brand-gold/30 font-semibold' : 'text-text-secondary hover:text-text-primary font-medium' }}"
                >
                    <span>Identity</span>
                    <span class="sr-only">Filosofi &amp; Identitas</span>
                </a>
                <a
                    href="{{ route('directory.index') }}"
                    class="px-3.5 py-1.5 rounded-full font-display text-xs lg:text-sm transition-colors {{ $isDirectoryContext ? 'text-brand-gold bg-brand-gold/10 border border-brand-gold/30 font-semibold' : 'text-text-secondary hover:text-text-primary font-medium' }}"
                >
                    <span>Directory</span>
                    <span class="sr-only">Direktori</span>
                </a>
                <a
                    href="{{ route('landing') }}#moments-section"
                    class="px-3.5 py-1.5 rounded-full font-display text-xs lg:text-sm transition-colors {{ $activePage === 'gallery' ? 'text-brand-gold bg-brand-gold/10 border border-brand-gold/30 font-semibold' : 'text-text-secondary hover:text-text-primary font-medium' }}"
                >
                    <span>Gallery</span>
                    <span class="sr-only">Galeri</span>
                </a>
            </nav>

            {{-- ── Right Block: Login / Session Action Button ── --}}
            <div class="hidden md:flex shrink-0 items-center justify-end gap-2.5">
                @if($isDirectoryContext)
                    <a
                        href="{{ route('landing') }}"
                        class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 text-xs font-mono uppercase tracking-wider text-text-primary bg-surface-elevated hover:border-brand-gold/40 border border-white/10 rounded-full transition-colors"
                    >
                        <span aria-hidden="true">&larr;</span>
                        <span>Kembali Ke Beranda</span>
                    </a>
                @endif

                @if($unlocked)
                    {{-- Authenticated Cohort: Lock / Logout Session Button --}}
                    <form method="POST" action="{{ route('lock') }}" class="inline-flex">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 px-4 py-1.5 rounded-full bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary font-display text-xs lg:text-sm font-semibold transition-colors cursor-pointer"
                            title="Kunci Kembali / Keluar Sesi"
                            aria-label="Kunci kembali sesi direktori"
                        >
                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" y1="12" x2="9" y2="12" />
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                @else
                    {{-- Guest State: Primary Blood Red Login / Unlock CTA Button --}}
                    <a
                        href="{{ route('unlock') }}"
                        class="inline-flex items-center justify-center gap-2 px-4 py-1.5 rounded-full bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary font-display text-xs lg:text-sm font-semibold transition-colors shadow-xs"
                    >
                        <svg class="size-3.5 text-brand-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <span>Login</span>
                        <span class="sr-only">Akses Direktori</span>
                    </a>
                @endif
            </div>

            {{-- ── Mobile Trigger (Login Action + Hamburger Button) ── --}}
            <div class="flex md:hidden items-center gap-2">
                @if(!$unlocked)
                    <a
                        href="{{ route('unlock') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-display font-semibold bg-brand-red hover:bg-brand-red-hover text-text-primary border border-brand-red-hover transition-colors"
                    >
                        <span>Login</span>
                    </a>
                @endif

                <button
                    type="button"
                    @click="mobileOpen = !mobileOpen"
                    class="p-2 text-text-primary hover:text-brand-gold bg-surface-elevated border border-white/10 rounded-full transition-colors cursor-pointer"
                    :aria-expanded="mobileOpen.toString()"
                    aria-controls="mobile-navigation-drawer"
                    aria-label="Toggle navigasi menu"
                >
                    <svg x-show="!mobileOpen" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="4" y1="6" x2="20" y2="6"></line>
                        <line x1="4" y1="12" x2="20" y2="12"></line>
                        <line x1="4" y1="18" x2="20" y2="18"></line>
                    </svg>
                    <svg x-cloak x-show="mobileOpen" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ── Mobile Navigation Drawer (Alpine.js Micro-Interaction) ── --}}
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
                    class="px-3.5 py-2.5 rounded-lg transition-colors {{ $activePage === 'home' ? 'text-brand-gold font-semibold bg-brand-gold/10 border border-brand-gold/30' : 'text-text-secondary hover:text-text-primary hover:bg-surface-elevated font-medium' }}"
                >
                    Home (Beranda)
                </a>
                <a
                    href="{{ route('identity') }}"
                    class="px-3.5 py-2.5 rounded-lg transition-colors {{ $activePage === 'identity' ? 'text-brand-gold font-semibold bg-brand-gold/10 border border-brand-gold/30' : 'text-text-secondary hover:text-text-primary hover:bg-surface-elevated font-medium' }}"
                >
                    Identity (Filosofi &amp; Identitas)
                </a>
                <a
                    href="{{ route('directory.index') }}"
                    class="px-3.5 py-2.5 rounded-lg transition-colors {{ $isDirectoryContext ? 'text-brand-gold font-semibold bg-brand-gold/10 border border-brand-gold/30' : 'text-text-secondary hover:text-text-primary hover:bg-surface-elevated font-medium' }}"
                >
                    Directory (Direktori)
                </a>
                <a
                    href="{{ route('landing') }}#moments-section"
                    @click="mobileOpen = false"
                    class="px-3.5 py-2.5 rounded-lg transition-colors {{ $activePage === 'gallery' ? 'text-brand-gold font-semibold bg-brand-gold/10 border border-brand-gold/30' : 'text-text-secondary hover:text-text-primary hover:bg-surface-elevated font-medium' }}"
                >
                    Gallery (Galeri)
                </a>
            </div>

            <div class="pt-3 border-t border-white/10 flex items-center justify-between gap-2">
                @if($unlocked)
                    <form method="POST" action="{{ route('lock') }}" class="w-full">
                        @csrf
                        <button
                            type="submit"
                            class="w-full py-2.5 px-4 rounded-lg bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary font-display font-semibold text-xs flex items-center justify-center gap-2 transition-colors"
                            title="Kunci Kembali / Keluar"
                        >
                            <span>Logout / Kunci Sesi</span>
                        </button>
                    </form>
                @else
                    <a
                        href="{{ route('unlock') }}"
                        class="w-full py-2.5 px-4 rounded-lg bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary font-display font-semibold text-xs flex items-center justify-center gap-2 transition-colors"
                    >
                        <span>Login Angkatan</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</header>
