<div
    x-data="{
        focusSearch(e) {
            const tag = document.activeElement?.tagName;
            if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(tag) && !document.activeElement?.isContentEditable) {
                e.preventDefault();
                this.$refs.searchInput?.focus();
            }
        }
    }"
    @keydown.window="focusSearch($event)"
    @reset-directory-filter.window="$wire.resetFilters()"
>
    {{-- Page Header --}}
    <header class="mb-8 border-b border-white/10 pb-6">
        <p class="font-display text-xs sm:text-sm font-semibold uppercase tracking-widest text-brand-gold mb-2">
            Direktori Angkatan &middot; COMETS 2026
        </p>

        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
            <div>
                <h1 class="font-display text-3xl sm:text-5xl font-bold text-text-primary tracking-tight leading-tight">
                    Direktori Mahasiswa Angkatan
                </h1>
                <p class="mt-3 font-body text-sm sm:text-base text-text-secondary leading-relaxed max-w-2xl">
                    Telusuri profil mahasiswa Teknik Komputer 2026 berdasarkan nama lengkap, nama panggilan, NIM, kota domisili, maupun kelas.
                </p>
            </div>

            <div class="shrink-0 flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('graph.index', $activeAspects !== [] ? ['aspects' => implode(',', $activeAspects)] : []) }}"
                    class="inline-flex items-center gap-2 min-h-10 px-4 py-2 rounded-xl bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover font-display text-xs font-semibold text-text-primary transition-colors"
                >
                    <svg class="size-4 text-brand-gold shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="2.5" />
                        <circle cx="5" cy="6" r="2" />
                        <circle cx="19" cy="6" r="2" />
                        <circle cx="5" cy="18" r="2" />
                        <circle cx="19" cy="18" r="2" />
                        <path stroke-linecap="round" d="m10 10.5-3.5-3M14 10.5l3.5-3M10 13.5l-3.5 3M14 13.5l3.5 3" />
                    </svg>
                    <span>Buka Graph View</span>
                    <span class="font-mono text-brand-gold" aria-hidden="true">&rarr;</span>
                </a>

                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-surface-card/85 border border-white/15 font-display text-xs font-semibold text-brand-gold">
                    <span>Total {{ $totalStudents }} Mahasiswa</span>
                    <span class="sr-only">{{ $totalStudents }} MAHASISWA TERDATA</span>
                </span>
            </div>
        </div>
    </header>

    {{-- Livewire Real-Time Search Bar & Class Filter Tabs --}}
    <section
        aria-label="Pencarian dan filter kelas mahasiswa"
        class="mb-8"
        x-data="{ showMbtiFilter: @js($activeAspects !== []) }"
    >
        <div class="rounded-2xl border border-white/10 bg-surface-card/80 p-4 sm:p-6 space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                {{-- Real-Time Search Input --}}
                <form wire:submit.prevent="$refresh" class="relative flex-1 min-w-0">
                    <label for="directory-search-input" class="sr-only">
                        Cari nama, panggilan, atau NIM mahasiswa
                    </label>
                    <div class="relative flex items-center">
                        <svg class="pointer-events-none absolute left-3.5 size-4 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-3.5-3.5" />
                        </svg>
                        <input
                            id="directory-search-input"
                            x-ref="searchInput"
                            type="search"
                            wire:model.live.debounce.250ms="search"
                            placeholder="Cari nama, panggilan, NIM, atau kota asal..."
                            class="w-full min-h-11 rounded-xl border border-white/15 bg-surface-elevated pl-10 pr-20 py-2.5 font-body text-sm text-text-primary placeholder:text-text-muted focus:border-brand-gold focus-visible:ring-2 focus-visible:ring-brand-gold/70 focus:outline-none transition-colors"
                        />
                        <div class="absolute right-2.5 flex items-center gap-2">
                            <span wire:loading wire:target="search, selectClass, toggleAspect, resetAspects, resetFilters, setPerPage, perPage, gotoPage, nextPage, previousPage" class="text-xs font-display text-brand-gold">
                                Memuat...
                            </span>
                            <kbd class="hidden sm:inline-flex items-center justify-center px-2 py-0.5 rounded bg-canvas-base border border-white/15 font-mono text-[11px] text-text-muted" aria-hidden="true">
                                /
                            </kbd>
                        </div>
                    </div>
                </form>

                {{-- Class Filter Tabs (>= 40px touch height) --}}
                <div
                    role="tablist"
                    aria-label="Filter berdasarkan kelas"
                    class="flex flex-wrap items-center gap-2"
                >
                    <button
                        type="button"
                        role="tab"
                        wire:click="selectClass('')"
                        aria-selected="{{ $class === '' ? 'true' : 'false' }}"
                        @class([
                            'min-h-10 px-3.5 py-2 rounded-xl border font-display text-xs font-semibold transition-colors cursor-pointer whitespace-nowrap',
                            'bg-brand-red text-text-primary border-brand-red-hover' => $class === '',
                            'bg-surface-elevated text-text-secondary border-white/10 hover:border-brand-gold/40 hover:text-text-primary' => $class !== '',
                        ])
                    >
                        All
                    </button>

                    @foreach ($filters as $filter)
                        <button
                            type="button"
                            role="tab"
                            wire:click="selectClass('{{ $filter }}')"
                            aria-selected="{{ $class === $filter ? 'true' : 'false' }}"
                            @class([
                                'min-h-10 px-3.5 py-2 rounded-xl border font-display text-xs font-semibold transition-colors cursor-pointer whitespace-nowrap inline-flex items-center gap-1.5',
                                'bg-brand-red text-text-primary border-brand-red-hover' => $class === $filter,
                                'bg-surface-elevated text-text-secondary border-white/10 hover:border-brand-gold/40 hover:text-text-primary' => $class !== $filter,
                            ])
                        >
                            <span>Kelas {{ $filter }}</span>
                            <span class="font-mono text-[11px] text-brand-gold">({{ $classCounts[$filter] }})</span>
                        </button>
                    @endforeach

                    <button
                        type="button"
                        @click="showMbtiFilter = !showMbtiFilter"
                        :aria-expanded="showMbtiFilter.toString()"
                        @class([
                            'min-h-10 px-3.5 py-2 rounded-xl border font-display text-xs font-semibold transition-colors cursor-pointer whitespace-nowrap inline-flex items-center gap-1.5',
                            'bg-brand-gold/15 text-brand-gold border-brand-gold/50' => $activeAspects !== [],
                            'bg-surface-elevated text-text-secondary border-white/10 hover:border-brand-gold/40 hover:text-brand-gold' => $activeAspects === [],
                        ])
                    >
                        <span>Filter MBTI</span>
                        @if ($activeAspects !== [])
                            <span class="font-mono text-[11px] text-text-primary">{{ implode('', $activeAspects) }}</span>
                        @endif
                    </button>
                </div>

            </div>

            {{-- Compact Collapsible MBTI 4-Axis Quick Filter Bar --}}
            @php
                $mbtiAxes = [
                    ['left' => 'E', 'leftName' => 'Extravert', 'right' => 'I', 'rightName' => 'Introvert'],
                    ['left' => 'N', 'leftName' => 'Intuition', 'right' => 'S', 'rightName' => 'Sensing'],
                    ['left' => 'T', 'leftName' => 'Thinking', 'right' => 'F', 'rightName' => 'Feeling'],
                    ['left' => 'J', 'leftName' => 'Judging', 'right' => 'P', 'rightName' => 'Perceiving'],
                ];
            @endphp
            <div
                x-cloak
                x-show="showMbtiFilter"
                x-transition
                class="pt-3 border-t border-white/10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5"
            >
                @foreach ($mbtiAxes as $pair)
                    @php
                        $left = $pair['left'];
                        $right = $pair['right'];
                        $leftStat = $aspectStats[$left] ?? ['active' => false, 'count' => 0];
                        $rightStat = $aspectStats[$right] ?? ['active' => false, 'count' => 0];
                    @endphp
                    <div class="rounded-xl border border-white/10 bg-canvas-base/80 p-1.5 grid grid-cols-2 gap-1.5">
                        <button
                            type="button"
                            wire:click="toggleAspect('{{ $left }}')"
                            aria-pressed="{{ $leftStat['active'] ? 'true' : 'false' }}"
                            data-aspect-pill="{{ $left }}"
                            @class([
                                'min-h-9 px-2.5 py-1 rounded-lg border font-display text-xs font-semibold transition-colors cursor-pointer flex items-center justify-between gap-1',
                                'bg-brand-red text-text-primary border-brand-gold/70' => $leftStat['active'],
                                'bg-surface-elevated text-text-secondary border-white/10 hover:border-brand-gold/45 hover:text-text-primary' => ! $leftStat['active'],
                            ])
                        >
                            <span>{{ $left }} &middot; {{ $pair['leftName'] }}</span>
                            <span class="font-mono text-[11px] {{ $leftStat['active'] ? 'text-brand-gold-glow' : 'text-brand-gold' }}">{{ $leftStat['count'] }}</span>
                        </button>
                        <button
                            type="button"
                            wire:click="toggleAspect('{{ $right }}')"
                            aria-pressed="{{ $rightStat['active'] ? 'true' : 'false' }}"
                            data-aspect-pill="{{ $right }}"
                            @class([
                                'min-h-9 px-2.5 py-1 rounded-lg border font-display text-xs font-semibold transition-colors cursor-pointer flex items-center justify-between gap-1',
                                'bg-brand-red text-text-primary border-brand-gold/70' => $rightStat['active'],
                                'bg-surface-elevated text-text-secondary border-white/10 hover:border-brand-gold/45 hover:text-text-primary' => ! $rightStat['active'],
                            ])
                        >
                            <span>{{ $right }} &middot; {{ $pair['rightName'] }}</span>
                            <span class="font-mono text-[11px] {{ $rightStat['active'] ? 'text-brand-gold-glow' : 'text-brand-gold' }}">{{ $rightStat['count'] }}</span>
                        </button>
                    </div>
                @endforeach
            </div>

            {{-- Active Filter Summary Bar & Dynamic Per-Page Control --}}
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pt-3 border-t border-white/10 text-xs text-text-secondary">
                <div class="flex flex-wrap items-center gap-2 font-body">
                    <span>
                        Menampilkan <strong class="text-brand-gold">{{ $students->count() }}</strong> dari <strong class="text-text-primary">{{ $students->total() }}</strong> mahasiswa
                    </span>
                    @if ($class !== '')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-brand-gold/15 border border-brand-gold/40 text-brand-gold font-medium">
                            Kelas {{ $class }}
                        </span>
                    @endif
                    @if ($activeAspects !== [])
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-brand-gold/15 border border-brand-gold/40 font-mono text-brand-gold font-semibold">
                            MBTI: {{ implode(' · ', $activeAspects) }}
                        </span>
                    @endif
                    @if (trim($search) !== '')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-brand-red/25 border border-brand-red-hover/60 text-text-primary font-medium">
                            “{{ trim($search) }}”
                        </span>
                    @endif

                    @if (trim($search) !== '' || $class !== '' || $activeAspects !== [])
                        <button
                            type="button"
                            wire:click="resetFilters"
                            class="inline-flex items-center min-h-8 px-2.5 py-1 rounded-lg border border-white/15 hover:border-brand-gold/50 font-display text-xs font-semibold text-brand-gold hover:text-brand-gold-glow transition-colors cursor-pointer"
                        >
                            Reset Filter
                        </button>
                    @endif
                </div>

                {{-- Always-visible Per-Page Control (presets + clamped custom numeric input) --}}
                <div
                    x-data="{
                        draft: {{ (int) $perPage }},
                        min: {{ (int) $minPerPage }},
                        max: {{ (int) $maxPerPage }},
                        commit() {
                            let parsed = parseInt(this.draft, 10);
                            if (Number.isNaN(parsed)) {
                                parsed = {{ (int) $perPage }};
                            }
                            parsed = Math.max(this.min, Math.min(this.max, parsed));
                            this.draft = parsed;
                            if (parsed !== {{ (int) $perPage }}) {
                                $wire.setPerPage(parsed);
                            }
                        }
                    }"
                    x-effect="draft = {{ (int) $perPage }}"
                    class="flex flex-wrap items-center gap-2 shrink-0"
                >
                    <span class="font-display text-xs text-text-muted">Per halaman:</span>

                    <div class="inline-flex items-center gap-1 rounded-xl border border-white/10 bg-canvas-base/80 p-1">
                        @foreach ($perPagePresets as $preset)
                            <button
                                type="button"
                                wire:click="setPerPage({{ $preset }})"
                                data-per-page-preset="{{ $preset }}"
                                aria-pressed="{{ $perPage === $preset ? 'true' : 'false' }}"
                                @class([
                                    'min-h-8 min-w-8 px-2 py-1 rounded-lg font-mono text-xs font-semibold transition-colors cursor-pointer',
                                    'bg-brand-red text-text-primary border border-brand-gold/60' => $perPage === $preset,
                                    'text-text-secondary hover:text-brand-gold border border-transparent' => $perPage !== $preset,
                                ])
                            >
                                {{ $preset }}
                            </button>
                        @endforeach
                    </div>

                    <form @submit.prevent="commit()" class="inline-flex items-center gap-1.5">
                        <label for="directory-per-page-input" class="sr-only">
                            Jumlah mahasiswa per halaman ({{ $minPerPage }} sampai {{ $maxPerPage }})
                        </label>
                        <input
                            id="directory-per-page-input"
                            type="number"
                            inputmode="numeric"
                            min="{{ $minPerPage }}"
                            max="{{ $maxPerPage }}"
                            x-model="draft"
                            @blur="commit()"
                            @change="commit()"
                            class="w-16 min-h-9 rounded-xl border border-white/15 bg-surface-elevated px-2.5 py-1 text-center font-mono text-xs font-semibold text-brand-gold focus:border-brand-gold focus:outline-none"
                        />
                        <span class="font-mono text-[11px] text-text-muted">({{ $minPerPage }}&ndash;{{ $maxPerPage }})</span>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Student Grid or Empty State --}}
    @php
        $studentItems = collect($students->items())->values();
        $firstItemIndex = $students->firstItem() ?? 1;
    @endphp

    @if ($students->count() > 0)
        <section aria-label="Daftar kartu profil mahasiswa" wire:loading.class="opacity-70 transition-opacity">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($studentItems as $idx => $student)
                    <div wire:key="student-{{ $student['nim'] }}">
                        <x-student-card
                            :student="$student"
                            :index="$firstItemIndex + $idx"
                        />
                    </div>
                @endforeach
            </div>
        </section>
    @else
        <x-directory-empty-state
            :searchQuery="$search"
            :selectedClass="$class"
            :useLivewireReset="true"
        />
    @endif

    {{-- Pagination Bar (>= 40px touch buttons) --}}
    @if ($students->hasPages())
        <nav
            aria-label="Navigasi halaman direktori"
            class="mt-10 pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4"
        >
            <p class="font-body text-xs text-text-muted">
                Halaman <span class="text-brand-gold font-semibold">{{ $students->currentPage() }}</span> dari <span class="text-text-primary">{{ $students->lastPage() }}</span>
            </p>

            <div class="flex flex-wrap items-center justify-center gap-2">
                @if ($students->onFirstPage())
                    <span class="inline-flex items-center justify-center min-h-10 gap-1.5 px-4 py-2 rounded-xl border border-white/10 bg-surface-card/50 font-display text-xs text-text-muted cursor-not-allowed select-none">
                        &larr; Sebelumnya
                    </span>
                @else
                    <button
                        type="button"
                        wire:click="previousPage"
                        class="inline-flex items-center justify-center min-h-10 gap-1.5 px-4 py-2 rounded-xl border border-white/15 bg-surface-card font-display text-xs font-semibold text-text-primary hover:border-brand-gold hover:text-brand-gold transition-colors cursor-pointer"
                    >
                        &larr; Sebelumnya
                    </button>
                @endif

                @foreach (range(max(1, $students->currentPage() - 2), min($students->lastPage(), $students->currentPage() + 2)) as $page)
                    @if ($page === $students->currentPage())
                        <span
                            aria-current="page"
                            class="inline-flex size-10 items-center justify-center rounded-xl bg-brand-red border border-brand-red-hover font-mono text-xs font-bold text-text-primary"
                        >
                            {{ $page }}
                        </span>
                    @else
                        <button
                            type="button"
                            wire:click="gotoPage({{ $page }})"
                            class="inline-flex size-10 items-center justify-center rounded-xl border border-white/15 bg-surface-card font-mono text-xs text-text-secondary hover:border-brand-gold hover:text-brand-gold transition-colors cursor-pointer"
                        >
                            {{ $page }}
                        </button>
                    @endif
                @endforeach

                @if ($students->hasMorePages())
                    <button
                        type="button"
                        wire:click="nextPage"
                        class="inline-flex items-center justify-center min-h-10 gap-1.5 px-4 py-2 rounded-xl border border-white/15 bg-surface-card font-display text-xs font-semibold text-text-primary hover:border-brand-gold hover:text-brand-gold transition-colors cursor-pointer"
                    >
                        Berikutnya &rarr;
                    </button>
                @else
                    <span class="inline-flex items-center justify-center min-h-10 gap-1.5 px-4 py-2 rounded-xl border border-white/10 bg-surface-card/50 font-display text-xs text-text-muted cursor-not-allowed select-none">
                        Berikutnya &rarr;
                    </span>
                @endif
            </div>
        </nav>
    @endif
</div>
