@props([
    'activePage' => 'home', // 'home' | 'identity' | 'unlock'
])

<footer class="bg-brand-deep border-t border-white/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">

            {{-- Brand + copyright --}}
            <div class="flex flex-col sm:flex-row items-center gap-3 text-sm text-white/35">
                <span class="font-semibold text-white/55">&copy; {{ date('Y') }} Teknik Komputer 2026</span>
                <flux:separator orientation="vertical" class="!hidden sm:!flex !h-4" />
                <span class="text-brand-mint/70 font-medium">Ascent</span>
            </div>

            {{-- Footer nav links --}}
            <nav class="flex items-center gap-1">
                <a href="/" class="px-3 py-1.5 text-xs rounded-md transition-colors {{ $activePage === 'home' ? 'text-brand-mint/60' : 'text-white/35 hover:text-brand-mint' }}">
                    Beranda
                </a>
                <a href="/identity" class="px-3 py-1.5 text-xs rounded-md transition-colors {{ $activePage === 'identity' ? 'text-brand-mint/60' : 'text-white/35 hover:text-brand-mint' }}">
                    Identitas
                </a>
                <a href="/unlock" class="px-3 py-1.5 text-xs rounded-md transition-colors {{ $activePage === 'unlock' ? 'text-brand-mint/60' : 'text-white/35 hover:text-brand-mint' }}">
                    Data
                </a>
                <a href="/#kontak" class="px-3 py-1.5 text-xs text-white/35 hover:text-brand-mint rounded-md transition-colors">Kontak</a>
                <flux:separator orientation="vertical" class="!hidden sm:!flex !h-4 !mx-1" />
                <flux:button href="/unlock" variant="ghost" size="xs" class="!text-white/35 !hover:text-brand-mint">
                    Masuk
                    <flux:icon icon="arrow-right" class="size-3 ms-1" />
                </flux:button>
            </nav>
        </div>

        {{-- Bottom tagline --}}
        <div class="mt-8 pt-6 border-t border-white/5 text-center">
            <p class="text-[11px] text-white/20 tracking-wide">
                Dibuat dengan &hearts; oleh angkatan Teknik Komputer 2026 — UNDIP
            </p>
        </div>
    </div>
</footer>
