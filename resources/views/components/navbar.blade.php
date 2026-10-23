@props([
    'activePage' => 'home', // 'home' | 'identity' | 'unlock'
])

<nav aria-label="Navigasi utama" class="sticky top-0 z-50 bg-white/90 backdrop-blur-xl border-b border-brand-deep/8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Brand --}}
            <a href="/" class="flex items-center gap-2.5 shrink-0 group">
                <div class="w-9 h-9 rounded-lg bg-brand-teal flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                    <span class="text-white font-bold text-sm tracking-tight">A</span>
                </div>
                <span class="text-[15px] font-semibold text-brand-deep tracking-tight">Tekkom 26</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-brand-mint/40 text-brand-deep border border-brand-teal/20">2026</span>
            </a>

            {{-- Navigation Links --}}
            <div class="hidden md:flex items-center gap-1">
                <a href="/" class="px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ $activePage === 'home' ? 'text-brand-teal bg-brand-teal/8' : 'text-brand-deep/70 hover:bg-brand-deep/5 hover:text-brand-deep' }}">
                    Beranda
                </a>
                <a href="/identity" class="px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ $activePage === 'identity' ? 'text-brand-teal bg-brand-teal/8' : 'text-brand-deep/70 hover:bg-brand-deep/5 hover:text-brand-deep' }}">
                    Identitas Angkatan
                </a>
                <a href="/directory" class="px-3.5 py-2 text-sm font-medium rounded-lg transition-colors {{ $activePage === 'unlock' ? 'text-brand-teal bg-brand-teal/8' : 'text-brand-deep/70 hover:bg-brand-deep/5 hover:text-brand-deep' }}">
                    Buku Angkatan
                </a>
                <a href="/#kontak" class="px-3.5 py-2 text-sm font-medium text-brand-deep/70 rounded-lg hover:bg-brand-deep/5 hover:text-brand-deep transition-colors">
                    Kontak
                </a>
            </div>

            {{-- CTA Button --}}
            <div class="shrink-0">
                <flux:button href="/directory" class="text-brand-mist" variant="primary" size="sm">
                    Buka Buku Angkatan
                    <flux:icon icon="arrow-right" class="size-3.5" />
                </flux:button>
            </div>
        </div>
    </div>
</nav>
