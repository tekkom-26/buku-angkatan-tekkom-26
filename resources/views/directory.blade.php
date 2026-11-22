<x-layouts.app activePage="directory" :title="'Direktori Mahasiswa — ' . $cohortName">

    @php
        $studentItems = collect($students->items())->values();
        $firstItemIndex = $students->firstItem() ?? 1;
    @endphp

    <main
        id="main-content"
        x-data="{
            searchQuery: @js((string) request('search', '')),
            selectedClass: @js((string) request('class', '')),
            resetFilters() {
                this.searchQuery = '';
                this.selectedClass = '';
            },
            submitFilters() {
                this.$refs.filterForm.requestSubmit();
            }
        }"
        @reset-directory-filter.window="resetFilters()"
        class="relative grow w-full bg-ambient-hero py-8 sm:py-12 lg:py-16 overflow-hidden"
    >
        {{-- Decorative Background Circuit Traces --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden select-none" aria-hidden="true">
            <svg class="hidden lg:block absolute right-0 top-6 w-80 h-80 text-brand-gold/20" viewBox="0 0 320 320" fill="none">
                <path d="M320 60 H210 L175 95 H105" stroke="currentColor" stroke-width="1.25" />
                <path d="M320 140 H240 L205 175 H145" stroke="currentColor" stroke-width="1" stroke-dasharray="4 4" class="text-brand-red-hover/40" />
                <circle cx="105" cy="95" r="3.5" class="fill-brand-gold" />
                <circle cx="145" cy="175" r="3.5" class="fill-brand-red-hover" />
            </svg>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ── 1. PAGE HEADER & ROSTER TELEMETRY ── --}}
            <header class="mb-8 border-b border-white/10 pb-6">
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-block size-2 rounded-full bg-brand-gold-glow animate-pulse" aria-hidden="true"></span>
                    <span class="font-mono text-[11px] sm:text-xs font-bold uppercase tracking-wider text-brand-gold">
                        &gt; // 03_DIRECTORY // STUDENT_LIST
                    </span>
                </div>

                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
                    <div>
                        <h1 class="font-display text-3xl sm:text-5xl font-bold text-text-primary tracking-tight leading-tight">
                            Direktori Mahasiswa Angkatan
                        </h1>
                        <div class="mt-3 border-l-4 border-l-brand-red pl-4 py-0.5 max-w-2xl">
                            <p class="font-body text-sm sm:text-base text-text-secondary leading-relaxed">
                                Arsip resmi koloni mahasiswa Teknik Komputer 2026 (COMETS) — telusuri simpul profil berdasarkan nama lengkap, nama panggilan, NIM, kota domisili, maupun pembagian kelas.
                            </p>
                        </div>
                    </div>

                    {{-- Monospace Telemetry Badges --}}
                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-surface-card/90 border border-brand-gold/40 font-mono text-xs font-bold uppercase tracking-wider text-brand-gold glow-gold-subtle">
                            <span>Total: {{ $totalStudents }} Mahasiswa</span>
                            <span class="sr-only">{{ $totalStudents }} MAHASISWA TERDATA</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-brand-red border border-brand-red-hover text-text-primary font-mono text-xs font-bold uppercase tracking-wider glow-red-subtle">
                            <span class="size-2 rounded-full bg-brand-gold animate-pulse" aria-hidden="true"></span>
                            <span>[MODE: UNLOCKED]</span>
                        </span>
                    </div>
                </div>
            </header>

            {{-- ── 2. INTERACTIVE TERMINAL SEARCH BAR & CLASS FILTER CHIPS ── --}}
            <section aria-label="Pencarian dan filter kelas mahasiswa" class="mb-8">
                <form
                    x-ref="filterForm"
                    method="GET"
                    action="{{ route('directory.index') }}"
                    class="relative overflow-hidden rounded-xl border border-brand-gold/30 bg-surface-card/90 p-4 sm:p-6 space-y-4 glow-gold-subtle"
                >
                    {{-- Top Dual-Tone Circuit Line --}}
                    <div class="absolute inset-x-0 top-0 h-1 flex" aria-hidden="true">
                        <div class="w-2/3 bg-brand-gold"></div>
                        <div class="w-1/3 bg-brand-red"></div>
                    </div>

                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pt-1">

                        {{-- Terminal Monospace Search Input Box --}}
                        <div class="relative flex-1 min-w-0">
                            <label for="directory-search-input" class="sr-only">
                                Cari nama, panggilan, atau NIM mahasiswa
                            </label>
                            <div class="relative flex items-center">
                                <span class="pointer-events-none absolute left-3.5 font-mono text-sm font-bold text-brand-gold" aria-hidden="true">
                                    &gt;_
                                </span>
                                <input
                                    id="directory-search-input"
                                    type="search"
                                    name="search"
                                    x-model.debounce.150ms="searchQuery"
                                    value="{{ request('search') }}"
                                    placeholder="Cari nama, panggilan, NIM, atau kota asal..."
                                    class="w-full min-h-11 rounded-lg border border-white/15 bg-surface-elevated pl-10 pr-24 py-2.5 font-mono text-xs sm:text-sm text-text-primary placeholder:text-text-muted focus:border-brand-gold focus:outline-none transition-colors"
                                />
                                <input type="hidden" name="class" :value="selectedClass" />
                                <button
                                    type="submit"
                                    class="absolute right-1.5 inline-flex items-center justify-center px-4 py-1.5 rounded-md bg-brand-red hover:bg-brand-red-hover text-text-primary font-display font-semibold text-xs border border-brand-red-hover transition-colors cursor-pointer"
                                >
                                    Cari
                                </button>
                            </div>
                        </div>

                        {{-- Class Filter Chips Bar: All, Kelas A, Kelas B, Kelas C, Kelas D --}}
                        <div
                            role="tablist"
                            aria-label="Filter berdasarkan kelas"
                            class="flex flex-wrap items-center gap-2"
                        >
                            {{-- All Chip --}}
                            <a
                                role="tab"
                                aria-selected="{{ request('class') ? 'false' : 'true' }}"
                                href="{{ route('directory.index', array_filter(['search' => request('search')])) }}"
                                @class([
                                    'px-3.5 py-2 rounded-lg border font-mono text-xs uppercase tracking-wider transition-all duration-180 cursor-pointer',
                                    'bg-brand-red text-text-primary border-brand-red-hover font-bold glow-red-subtle' => ! request('class'),
                                    'bg-surface-elevated text-text-secondary border-white/10 hover:border-brand-gold/40 hover:text-text-primary' => request('class'),
                                ])
                            >
                                All
                            </a>

                            @foreach ($filters as $filter)
                                <a
                                    role="tab"
                                    aria-selected="{{ request('class') === $filter ? 'true' : 'false' }}"
                                    href="{{ route('directory.index', array_filter(['search' => request('search'), 'class' => $filter])) }}"
                                    @class([
                                        'px-3.5 py-2 rounded-lg border font-mono text-xs uppercase tracking-wider transition-all duration-180 cursor-pointer',
                                        'bg-brand-red text-text-primary border-brand-red-hover font-bold glow-red-subtle' => request('class') === $filter,
                                        'bg-surface-elevated text-text-secondary border-white/10 hover:border-brand-gold/40 hover:text-text-primary' => request('class') !== $filter,
                                    ])
                                >
                                    Kelas {{ $filter }}
                                    <span class="ml-1 text-[10px] text-brand-gold">({{ $classCounts[$filter] }})</span>
                                </a>
                            @endforeach
                        </div>

                    </div>

                    {{-- Active Filter Summary & Status Strip --}}
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-3 border-t border-white/10 text-xs text-text-secondary">
                        <div class="flex flex-wrap items-center gap-2 font-mono text-[11px]">
                            <span>
                                MENAMPILKAN <strong class="text-brand-gold">{{ $students->count() }}</strong> DARI <strong class="text-text-primary">{{ $students->total() }}</strong> MAHASISWA
                            </span>
                            <template x-if="selectedClass !== ''">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-brand-gold/15 border border-brand-gold/40 text-brand-gold font-semibold">
                                    FILTER: KELAS <span class="ml-1" x-text="selectedClass"></span>
                                </span>
                            </template>
                            <template x-if="searchQuery.trim() !== ''">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-brand-red/30 border border-brand-red-hover text-text-primary font-semibold">
                                    KATA KUNCI: "<span x-text="searchQuery.trim()"></span>"
                                </span>
                            </template>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                x-show="searchQuery.trim() !== '' || selectedClass !== ''"
                                @click="resetFilters()"
                                style="display: none;"
                                class="font-mono text-[11px] font-semibold text-brand-gold hover:text-brand-gold-glow underline underline-offset-2 cursor-pointer"
                            >
                                Reset Filter Instan
                            </button>
                            @if (request('search') || request('class'))
                                <a
                                    href="{{ route('directory.index') }}"
                                    class="font-mono text-[11px] font-semibold text-brand-gold hover:text-brand-gold-glow underline underline-offset-2"
                                >
                                    Reset Semua
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </section>

            {{-- ── 3. STUDENT DIRECTORY 3:4 PORTRAIT CARD GRID & EMPTY STATE ── --}}
            @if ($students->count() > 0)
                <section aria-label="Daftar kartu profil mahasiswa">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($studentItems as $idx => $student)
                            <div>
                                <x-student-card
                                    :student="$student"
                                    :index="$firstItemIndex + $idx"
                                />
                            </div>
                        @endforeach
                    </div>
                </section>
            @else
                {{-- Server-Side Empty State (Bee-ner Mascot Theme) --}}
                <x-directory-empty-state
                    :searchQuery="request('search')"
                    :selectedClass="request('class')"
                />
            @endif

            {{-- ── 4. CENTERED PAGINATION BAR ── --}}
            @if ($students->hasPages())
                <nav
                    aria-label="Navigasi halaman direktori"
                    class="mt-10 pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4"
                >
                    <p class="font-mono text-xs uppercase tracking-wider text-text-muted">
                        HALAMAN <span class="text-brand-gold font-bold">{{ $students->currentPage() }}</span> DARI <span class="text-text-primary">{{ $students->lastPage() }}</span>
                    </p>

                    <div class="flex flex-wrap items-center justify-center gap-2">
                        {{-- Previous Page Link --}}
                        @if ($students->onFirstPage())
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-white/10 bg-surface-card/50 font-mono text-xs text-text-muted cursor-not-allowed select-none">
                                &larr; Sebelumnya
                            </span>
                        @else
                            <a
                                href="{{ $students->withQueryString()->previousPageUrl() }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-white/15 bg-surface-card font-mono text-xs font-semibold text-text-primary hover:border-brand-gold hover:text-brand-gold transition-colors"
                            >
                                &larr; Sebelumnya
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach ($students->getUrlRange(max(1, $students->currentPage() - 2), min($students->lastPage(), $students->currentPage() + 2)) as $page => $url)
                            @if ($page == $students->currentPage())
                                <span
                                    aria-current="page"
                                    class="inline-flex size-9 items-center justify-center rounded-lg bg-brand-red border border-brand-red-hover font-mono text-xs font-bold text-text-primary glow-red-subtle"
                                >
                                    {{ $page }}
                                </span>
                            @else
                                <a
                                    href="{{ $students->withQueryString()->url($page) }}"
                                    class="inline-flex size-9 items-center justify-center rounded-lg bg-surface-card border border-white/10 font-mono text-xs font-medium text-text-secondary hover:border-brand-gold/50 hover:text-brand-gold transition-colors"
                                >
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($students->hasMorePages())
                            <a
                                href="{{ $students->withQueryString()->nextPageUrl() }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-white/15 bg-surface-card font-mono text-xs font-semibold text-text-primary hover:border-brand-gold hover:text-brand-gold transition-colors"
                            >
                                Selanjutnya &rarr;
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-white/10 bg-surface-card/50 font-mono text-xs text-text-muted cursor-not-allowed select-none">
                                Selanjutnya &rarr;
                            </span>
                        @endif
                    </div>
                </nav>
            @endif

        </div>
    </main>
</x-layouts.app>
