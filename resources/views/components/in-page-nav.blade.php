@props([
    'items' => [
        ['id' => 'hero', 'label' => '[1. HERO]'],
        ['id' => 'stats', 'label' => '[2. STATS]'],
        ['id' => 'values', 'label' => '[3. VALUES]'],
        ['id' => 'vision', 'label' => '[4. VISION]'],
        ['id' => 'directory', 'label' => '[5. DIRECTORY]'],
    ],
])

@php
    $defaultActiveSection = $items[0]['id'] ?? 'hero';
@endphp

{{-- ==========================================================================
     IN-PAGE COLLAPSIBLE SIDE NAVIGATION DRAWER (COMETS 2026 DARK CHARCOAL)
     ========================================================================== --}}
<aside
    aria-label="Navigasi bagian halaman"
    x-data="{
        open: false,
        activeSection: '{{ $defaultActiveSection }}',
        sections: @js(collect($items)->pluck('id')->values()),
        init() {
            this.updateActiveSection();
        },
        updateActiveSection() {
            const offset = window.innerHeight * 0.35;
            for (let i = this.sections.length - 1; i >= 0; i--) {
                const el = document.getElementById(this.sections[i]);
                if (el) {
                    const rect = el.getBoundingClientRect();
                    if (rect.top <= offset) {
                        this.activeSection = this.sections[i];
                        return;
                    }
                }
            }
        },
        scrollToSection(id) {
            this.activeSection = id;
            const target = document.getElementById(id);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, '', '#' + id);
            }
        }
    }"
    @scroll.window.throttle.100ms="updateActiveSection()"
    @keydown.escape.window="open = false"
    class="fixed right-0 top-1/2 -translate-y-1/2 z-40 flex items-center"
>
    {{-- ── STATE 1: COLLAPSED (Minimalist Handle) ── --}}
    <button
        type="button"
        x-show="!open"
        @click="open = true"
        aria-label="Buka navigasi halaman [NAV]"
        :aria-expanded="open ? 'true' : 'false'"
        class="group flex flex-col items-center gap-1.5 py-3 px-2.5 rounded-l-xl bg-surface-card border border-r-0 border-white/10 text-text-primary hover:border-brand-gold/40 hover:text-brand-gold transition-all duration-180 cursor-pointer"
    >
        <span class="font-mono text-xs font-bold uppercase tracking-wider">
            [NAV]
        </span>
        <span class="sr-only">[X] [ICON: CHEVRON_LEFT]</span>
        <svg class="size-4 stroke-[1.75] stroke-current group-hover:-translate-x-0.5 transition-transform duration-180" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
        </svg>
    </button>

    {{-- ── STATE 2: EXPANDED (Charcoal Geometric Drawer Card) ── --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-180"
        x-transition:enter-start="opacity-0 translate-x-4"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-4"
        @click.outside="open = false"
        style="display: none;"
        class="mr-3 w-64 max-w-[calc(100vw-1.5rem)] rounded-xl bg-surface-card border border-brand-gold/30 shadow-lg overflow-hidden"
    >
        <div class="flex items-center justify-between gap-2 px-3.5 py-2.5 border-b border-white/10 bg-canvas-base">
            <span class="font-mono text-xs font-bold uppercase tracking-wider text-brand-gold">
                [PAGE NAV]
            </span>
            <div class="flex items-center gap-2">
                <span class="font-mono text-xs uppercase tracking-wider text-text-muted">
                    [IN-PAGE]
                </span>
                <button
                    type="button"
                    @click="open = false"
                    aria-label="Tutup navigasi halaman"
                    class="font-mono text-xs font-bold text-text-secondary hover:text-brand-gold px-1 rounded transition-colors cursor-pointer"
                >
                    [X]
                </button>
            </div>
        </div>

        <nav aria-label="Daftar bagian halaman" class="p-2 space-y-1">
            @foreach ($items as $item)
                <a
                    href="#{{ $item['id'] }}"
                    @click.prevent="scrollToSection('{{ $item['id'] }}')"
                    :class="activeSection === '{{ $item['id'] }}'
                        ? 'bg-brand-gold/15 text-brand-gold font-bold border-brand-gold/40'
                        : 'bg-transparent text-text-secondary border-transparent hover:bg-surface-elevated hover:text-text-primary font-medium'"
                    class="flex items-center justify-between gap-2 px-3 py-2 rounded-lg border font-mono text-xs uppercase tracking-wider transition-all duration-180"
                >
                    <span>{{ $item['label'] }}</span>
                    <span
                        x-show="activeSection === '{{ $item['id'] }}'"
                        class="font-mono text-[10px] uppercase tracking-wider text-brand-gold"
                    >
                        (ACTIVE)
                    </span>
                </a>
            @endforeach
        </nav>
    </div>
</aside>
