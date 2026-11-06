@props([
    'activePage' => 'home', // 'home' | 'identity' | 'directory' | 'unlock' | 'student' | 'contact'
    'isUnlocked' => null,
])

@php
    $unlocked = $isUnlocked ?? (
        request()->attributes->get('student_role') === 'student'
        || app(\App\Services\StudentSessionService::class)->resolveRequestRole(request()) === 'student'
    );

    $isDirectoryContext = in_array($activePage, ['directory', 'student', 'students.show', 'unlock'])
        || request()->routeIs('directory.*')
        || request()->routeIs('students.*')
        || request()->routeIs('unlock');
@endphp

<header
    x-data="{ mobileOpen: false }"
    @keydown.escape.window="mobileOpen = false"
    class="sticky top-0 z-50 w-full border-b border-surface-border bg-surface-canvas/95 backdrop-blur-md transition-colors"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">

            {{-- ── Left Block: Brand Identity (stich-uiux/Navbar.png) ── --}}
            <div class="w-auto md:w-[320px] shrink-0 flex items-center">
                <a
                    href="{{ route('landing') }}"
                    class="inline-flex items-center gap-3 group focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary-500 rounded-lg p-1 -m-1"
                    aria-label="Beranda ANGKATAN // TEKKOM 2026"
                >
                    {{-- Circular wireframe logo badge [x] --}}
                    <div
                        class="size-9 rounded-full border border-surface-border bg-surface-card flex items-center justify-center font-mono text-xs text-primary-900 font-medium shrink-0 shadow-2xs group-hover:border-secondary-500 transition-colors"
                        title="[x] Logo Angkatan"
                    >
                        [x]
                    </div>

                    <span class="text-sm font-bold tracking-tight text-primary-900 group-hover:text-primary-800 transition-colors">
                        ANGKATAN // TEKKOM 2026
                    </span>
                </a>
            </div>

            {{-- ── Center Block: Nav Links (Desktop, stich-uiux/Navbar.png) ── --}}
            <nav aria-label="Navigasi Utama" class="hidden md:flex flex-1 items-center justify-center gap-6 lg:gap-8 text-sm">
                <a
                    href="{{ route('landing') }}"
                    class="transition-colors {{ $activePage === 'home' ? 'text-primary-900 font-semibold' : 'text-primary-700 hover:text-primary-900 font-medium' }}"
                >
                    Beranda
                </a>
                <a
                    href="{{ route('identity') }}"
                    class="transition-colors {{ $activePage === 'identity' ? 'text-primary-900 font-semibold' : 'text-primary-700 hover:text-primary-900 font-medium' }}"
                >
                    Filosofi &amp; Identitas
                </a>
                <a
                    href="{{ route('directory.index') }}"
                    class="transition-colors {{ in_array($activePage, ['directory', 'unlock', 'student', 'students.show']) ? 'text-primary-900 font-semibold' : 'text-primary-700 hover:text-primary-900 font-medium' }}"
                >
                    Direktori
                </a>
                <a
                    href="/#galeri"
                    class="transition-colors {{ $activePage === 'gallery' ? 'text-primary-900 font-semibold' : 'text-primary-700 hover:text-primary-900 font-medium' }}"
                >
                    Galeri
                </a>
            </nav>

            {{-- ── Right Block: Actions (Desktop, stich-uiux/Navbar.png) ── --}}
            <div class="hidden md:flex w-[320px] shrink-0 items-center justify-end gap-2.5">
                @if($isDirectoryContext)
                    {{-- Directory & Detail Siswa Context: "← Kembali Ke Beranda" --}}
                    <a
                        href="{{ route('landing') }}"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-medium text-primary-900 bg-secondary-500 hover:bg-secondary-700 rounded-lg transition-colors shadow-2xs focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary-500"
                    >
                        <span aria-hidden="true">&larr;</span>
                        <span>Kembali Ke Beranda</span>
                    </a>
                @else
                    {{-- Landing, Identity, 404 Context: "Akses Direktori →" --}}
                    <a
                        href="{{ route('directory.index') }}"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-medium text-primary-900 bg-secondary-500 hover:bg-secondary-700 rounded-lg transition-colors shadow-2xs focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary-500"
                    >
                        <span>Akses Direktori</span>
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                @endif

                {{-- Authenticated Cohort: Square Logout / Relock Icon Button [->] --}}
                @if($unlocked)
                    <form method="POST" action="{{ route('lock') }}" class="inline-flex">
                        @csrf
                        <button
                            type="submit"
                            class="size-9 rounded-lg bg-surface-card hover:bg-surface-subtle border border-surface-border text-primary-900 flex items-center justify-center transition-colors shadow-2xs focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary-500"
                            title="Kunci Kembali / Keluar Sesi"
                            aria-label="Kunci kembali sesi direktori"
                        >
                            <svg class="size-4.5 text-primary-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                <polyline points="16 17 21 12 16 7" />
                                <line x1="21" y1="12" x2="9" y2="12" />
                            </svg>
                        </button>
                    </form>
                @endif
            </div>

            {{-- ── Mobile Trigger (Hamburger Button) ── --}}
            <div class="flex md:hidden items-center gap-2">
                @if($unlocked)
                    <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-mono bg-secondary-100 text-secondary-900 border border-secondary-300">
                        <span class="size-1.5 rounded-full bg-secondary-500"></span>
                        <span>ACTIVE</span>
                    </div>
                @endif
                <button
                    type="button"
                    @click="mobileOpen = !mobileOpen"
                    class="p-2 text-primary-900 hover:text-primary-800 bg-surface-card border border-surface-border rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary-500 transition-colors"
                    :aria-expanded="mobileOpen.toString()"
                    aria-controls="mobile-navigation-drawer"
                    aria-label="Toggle navigasi menu"
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
    </div>

    {{-- ── Mobile Navigation Drawer (Alpine.js Client-Side Micro-Interaction) ── --}}
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
        class="md:hidden border-t border-surface-border bg-surface-canvas px-4 pt-3 pb-6 space-y-3 shadow-lg"
    >
        <div class="flex flex-col space-y-1 text-sm">
            <a
                href="{{ route('landing') }}"
                class="px-3.5 py-2.5 rounded-lg transition-colors {{ $activePage === 'home' ? 'text-primary-900 font-semibold bg-surface-subtle border border-surface-border' : 'text-primary-700 hover:text-primary-900 hover:bg-surface-subtle/50 font-medium' }}"
            >
                Beranda
            </a>
            <a
                href="{{ route('identity') }}"
                class="px-3.5 py-2.5 rounded-lg transition-colors {{ $activePage === 'identity' ? 'text-primary-900 font-semibold bg-surface-subtle border border-surface-border' : 'text-primary-700 hover:text-primary-900 hover:bg-surface-subtle/50 font-medium' }}"
            >
                Filosofi &amp; Identitas
            </a>
            <a
                href="{{ route('directory.index') }}"
                class="px-3.5 py-2.5 rounded-lg transition-colors {{ in_array($activePage, ['directory', 'unlock', 'student', 'students.show']) ? 'text-primary-900 font-semibold bg-surface-subtle border border-surface-border' : 'text-primary-700 hover:text-primary-900 hover:bg-surface-subtle/50 font-medium' }}"
            >
                Direktori
            </a>
            <a
                href="/#galeri"
                class="px-3.5 py-2.5 rounded-lg transition-colors {{ $activePage === 'gallery' ? 'text-primary-900 font-semibold bg-surface-subtle border border-surface-border' : 'text-primary-700 hover:text-primary-900 hover:bg-surface-subtle/50 font-medium' }}"
            >
                Galeri
            </a>
        </div>

        <div class="pt-3 border-t border-surface-border flex items-center gap-2">
            @if($isDirectoryContext)
                <a
                    href="{{ route('landing') }}"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm font-medium text-primary-900 bg-secondary-500 hover:bg-secondary-700 rounded-lg transition-colors shadow-2xs"
                >
                    <span aria-hidden="true">&larr;</span>
                    <span>Kembali Ke Beranda</span>
                </a>
            @else
                <a
                    href="{{ route('directory.index') }}"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm font-medium text-primary-900 bg-secondary-500 hover:bg-secondary-700 rounded-lg transition-colors shadow-2xs"
                >
                    <span>Akses Direktori</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            @endif

            @if($unlocked)
                <form method="POST" action="{{ route('lock') }}" class="shrink-0">
                    @csrf
                    <button
                        type="submit"
                        class="size-10 rounded-lg bg-surface-card hover:bg-surface-subtle border border-surface-border text-primary-900 flex items-center justify-center transition-colors shadow-2xs"
                        title="Kunci Kembali / Keluar"
                        aria-label="Kunci kembali sesi direktori"
                    >
                        <svg class="size-5 text-primary-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                            <polyline points="16 17 21 12 16 7" />
                            <line x1="21" y1="12" x2="9" y2="12" />
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    </div>
</header>
