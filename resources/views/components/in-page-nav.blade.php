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
     IN-PAGE COLLAPSIBLE SIDE NAVIGATION DRAWER
     Mengacu pada GitHub Issue #11, DESIGN.md Bagian 6.C, dan DOCS_COLOR_CONVENTION.md
     Figma Reference: Node 20:41 (In-Page Nav Drawer) & Node 19:956 (Chevron Icon 16x16px)
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
        class="group flex flex-col items-center gap-1.5 py-3 px-2.5 rounded-l-xl bg-white border border-r-0 border-surface-border text-primary-900 shadow-xs hover:bg-surface-card hover:border-secondary-500 transition-all duration-180 cursor-pointer focus-visible:outline-2 focus-visible:outline-secondary-500 focus-visible:outline-offset-2"
    >
        <span class="font-mono text-xs font-bold uppercase tracking-[0.05em] text-primary-900">
            [NAV]
        </span>
        <span class="sr-only">[X] [ICON: CHEVRON_LEFT]</span>
        {{-- Wireframe Monoline Chevron Left 16x16px (Figma Node 19:956) --}}
        <svg class="size-4 stroke-[1.75] stroke-current text-primary-900 group-hover:-translate-x-0.5 transition-transform duration-180" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
        </svg>
    </button>

    {{-- ── STATE 2: EXPANDED (Monochromatic Sharp Drawer Card) ── --}}
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
        class="mr-3 w-64 max-w-[calc(100vw-1.5rem)] rounded-xl bg-white border border-primary-900 shadow-xs overflow-hidden"
    >
        {{-- Baris Header: [PAGE NAV] (Kiri) & [IN-PAGE] + Tombol Tutup (Kanan), Dipisahkan Hairline Divider --}}
        <div class="flex items-center justify-between gap-2 px-3.5 py-2.5 border-b border-surface-border bg-surface-canvas">
            <span class="font-mono text-xs font-bold uppercase tracking-[0.05em] text-primary-900">
                [PAGE NAV]
            </span>
            <div class="flex items-center gap-2">
                <span class="font-mono text-xs uppercase tracking-[0.05em] text-primary-500">
                    [IN-PAGE]
                </span>
                <button
                    type="button"
                    @click="open = false"
                    aria-label="Tutup navigasi halaman"
                    class="font-mono text-xs font-bold text-primary-700 hover:text-primary-900 px-1 rounded transition-colors duration-180 cursor-pointer focus-visible:outline-2 focus-visible:outline-secondary-500 focus-visible:outline-offset-2"
                >
                    [X]
                </button>
            </div>
        </div>

        {{-- Daftar Item Berkurung Siku Monospace --}}
        <nav aria-label="Daftar bagian halaman" class="p-2 space-y-1">
            @foreach ($items as $item)
                <a
                    href="#{{ $item['id'] }}"
                    @click.prevent="scrollToSection('{{ $item['id'] }}')"
                    :class="activeSection === '{{ $item['id'] }}'
                        ? 'bg-primary-900 text-white font-bold border-primary-900'
                        : 'bg-white text-primary-900 border-transparent hover:bg-surface-card hover:border-secondary-500 font-medium'"
                    class="flex items-center justify-between gap-2 px-3 py-2 rounded-lg border font-mono text-xs uppercase tracking-[0.05em] transition-all duration-180 focus-visible:outline-2 focus-visible:outline-secondary-500 focus-visible:outline-offset-2"
                >
                    <span>{{ $item['label'] }}</span>
                    <span
                        x-show="activeSection === '{{ $item['id'] }}'"
                        class="font-mono text-[10px] uppercase tracking-[0.05em] text-white"
                    >
                        (ACTIVE)
                    </span>
                </a>
            @endforeach
        </nav>
    </div>
</aside>
