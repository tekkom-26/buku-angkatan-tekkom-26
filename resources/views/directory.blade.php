<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Direktori Mahasiswa — Ascent Teknik Komputer 2026</title>
        @fonts
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        @livewireStyles
    </head>
    <body class="min-h-screen overflow-x-hidden bg-surface-canvas text-primary-900 antialiased">

        {{-- Global Navbar --}}
        <x-navbar activePage="unlock" />

        @php
            $studentItems = collect($students->items())->values();
            $firstItemIndex = $students->firstItem() ?? 1;
        @endphp

        <main
            id="main-content"
            x-data="{
                searchQuery: @js((string) request('search', '')),
                selectedClass: @js((string) request('class', '')),
                studentsMeta: @js($studentItems->map(fn ($s, $idx) => [
                    'idx' => $idx,
                    'nim' => strtolower((string) ($s['nim'] ?? '')),
                    'name' => strtolower((string) ($s['name'] ?? $s['nickname'] ?? '')),
                    'nickname' => strtolower((string) ($s['nickname'] ?? '')),
                    'city' => strtolower((string) ($s['origin_city'] ?? '')),
                    'class_name' => (string) ($s['class_name'] ?? ''),
                ])->values()),
                matchesFilter(idx) {
                    const item = this.studentsMeta[idx];
                    if (!item) return true;
                    const q = this.searchQuery.trim().toLowerCase();
                    const matchesClass = !this.selectedClass || item.class_name === this.selectedClass;
                    const matchesSearch = !q
                        || item.nim.includes(q)
                        || item.name.includes(q)
                        || item.nickname.includes(q)
                        || item.city.includes(q);
                    return matchesClass && matchesSearch;
                },
                get visibleCount() {
                    return this.studentsMeta.filter((_, idx) => this.matchesFilter(idx)).length;
                },
                resetFilters() {
                    this.searchQuery = '';
                    this.selectedClass = '';
                }
            }"
            @reset-directory-filter.window="resetFilters()"
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-14"
        >

            {{-- ── 1. PAGE HEADER & ROSTER TELEMETRY ── --}}
            <header class="mb-8 border-b border-surface-border pb-6">
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-block size-2 rounded-full bg-secondary-500" aria-hidden="true"></span>
                    <span class="font-mono text-[11px] sm:text-xs font-bold uppercase tracking-[0.05em] text-primary-700">
                        // 03_DIRECTORY // STUDENT_ROSTER
                    </span>
                </div>

                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4">
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold text-primary-900 tracking-[-0.025em] leading-tight">
                            Direktori Mahasiswa Angkatan
                        </h1>
                        <p class="mt-2 text-sm sm:text-base text-primary-900/80 max-w-2xl leading-relaxed">
                            Arsip resmi anggota angkatan Teknik Komputer 2026 — telusuri profil berdasarkan nama, NIM, maupun pembagian kelas.
                        </p>
                    </div>

                    {{-- Monospace Telemetry Badges --}}
                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-surface-card border border-surface-border font-mono text-xs font-bold uppercase tracking-[0.05em] text-primary-900">
                            <span>{{ $totalStudents }} MAHASISWA TERDATA</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-highlight-500 text-highlight-900 font-mono text-xs font-bold uppercase tracking-[0.05em]">
                            <span class="size-2 rounded-full bg-highlight-900" aria-hidden="true"></span>
                            <span>[MODE: UNLOCKED]</span>
                        </span>
                    </div>
                </div>
            </header>

            {{-- ── 2. INTERACTIVE SEARCH BAR & CLASS FILTER CHIPS (Figma Node 1:872) ── --}}
            <section aria-label="Pencarian dan filter kelas mahasiswa" class="mb-8">
                <form
                    method="GET"
                    action="{{ route('directory.index') }}"
                    class="rounded-2xl border border-surface-border bg-white p-4 sm:p-5 shadow-xs space-y-4"
                >
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                        {{-- Search Input Box (Figma Node 1:872) --}}
                        <div class="relative flex-1 min-w-0">
                            <label for="directory-search-input" class="sr-only">
                                Cari nama, panggilan, atau NIM mahasiswa
                            </label>
                            <div class="relative flex items-center">
                                <svg class="pointer-events-none absolute left-3.5 size-4 stroke-[2] stroke-current text-primary-500" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                                <input
                                    id="directory-search-input"
                                    type="search"
                                    name="search"
                                    x-model.debounce.150ms="searchQuery"
                                    value="{{ request('search') }}"
                                    placeholder="Cari nama, panggilan, atau NIM mahasiswa..."
                                    class="w-full min-h-11 rounded-lg border border-surface-border bg-surface-canvas pl-10 pr-24 py-2.5 text-sm text-primary-900 placeholder:text-primary-500 focus:bg-white focus:border-secondary-500 focus:outline-2 focus:outline-secondary-500 focus:outline-offset-2 transition-colors duration-180"
                                />
                                <input type="hidden" name="class" :value="selectedClass" />
                                <button
                                    type="submit"
                                    class="absolute right-1.5 inline-flex items-center justify-center px-3.5 py-1.5 rounded-md bg-secondary-500 text-primary-900 font-semibold text-xs border border-surface-border hover:bg-secondary-700 hover:text-white transition-colors duration-180 cursor-pointer"
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
                            <button
                                type="button"
                                role="tab"
                                :aria-selected="selectedClass === '' ? 'true' : 'false'"
                                @click="selectedClass = ''"
                                :class="selectedClass === ''
                                    ? 'bg-secondary-500 text-primary-900 border-primary-900/20 font-semibold shadow-2xs'
                                    : 'bg-surface-card text-primary-700 border-surface-border hover:bg-surface-subtle hover:text-primary-900'"
                                class="px-3.5 py-2 rounded-lg border font-mono text-xs uppercase tracking-[0.05em] transition-all duration-180 cursor-pointer focus-visible:outline-2 focus-visible:outline-secondary-500 focus-visible:outline-offset-2"
                            >
                                All
                            </button>

                            @foreach ($filters as $filter)
                                <button
                                    type="button"
                                    role="tab"
                                    :aria-selected="selectedClass === '{{ $filter }}' ? 'true' : 'false'"
                                    @click="selectedClass = '{{ $filter }}'"
                                    :class="selectedClass === '{{ $filter }}'
                                        ? 'bg-secondary-500 text-primary-900 border-primary-900/20 font-semibold shadow-2xs'
                                        : 'bg-surface-card text-primary-700 border-surface-border hover:bg-surface-subtle hover:text-primary-900'"
                                    class="px-3.5 py-2 rounded-lg border font-mono text-xs uppercase tracking-[0.05em] transition-all duration-180 cursor-pointer focus-visible:outline-2 focus-visible:outline-secondary-500 focus-visible:outline-offset-2"
                                >
                                    Kelas {{ $filter }}
                                </button>
                            @endforeach
                        </div>

                    </div>

                    {{-- Active Filter Summary & Status Strip --}}
                    <div class="flex flex-wrap items-center justify-between gap-2 pt-3 border-t border-surface-border text-xs text-primary-700">
                        <div class="flex flex-wrap items-center gap-2 font-mono text-[11px]">
                            <span>
                                MENAMPILKAN <strong class="text-primary-900" x-text="visibleCount">{{ $students->count() }}</strong> DARI <strong class="text-primary-900">{{ $students->total() }}</strong> MAHASISWA
                            </span>
                            <template x-if="selectedClass !== ''">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-surface-card border border-surface-border text-primary-900 font-semibold">
                                    FILTER: KELAS <span class="ml-1" x-text="selectedClass"></span>
                                </span>
                            </template>
                            <template x-if="searchQuery.trim() !== ''">
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-surface-card border border-surface-border text-primary-900 font-semibold">
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
                                class="font-mono text-[11px] font-semibold text-secondary-700 hover:text-primary-900 underline underline-offset-2 cursor-pointer"
                            >
                                Reset Filter Instan
                            </button>
                            @if (request('search') || request('class'))
                                <a
                                    href="{{ route('directory.index') }}"
                                    class="font-mono text-[11px] font-semibold text-primary-700 hover:text-primary-900 underline underline-offset-2"
                                >
                                    Reset Semua
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </section>

            {{-- ── 3. STUDENT DIRECTORY 3-COLUMN CARD GRID & EMPTY STATE ── --}}
            @if ($students->count() > 0)
                <section aria-label="Daftar kartu profil mahasiswa">
                    <div
                        x-show="visibleCount > 0"
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                    >
                        @foreach ($studentItems as $idx => $student)
                            <div
                                x-show="matchesFilter({{ $idx }})"
                                x-transition.opacity.duration.150ms
                            >
                                <x-student-card
                                    :student="$student"
                                    :index="$firstItemIndex + $idx"
                                />
                            </div>
                        @endforeach
                    </div>

                    {{-- Client-Side Instant Filter Empty State (Figma Node 34:1067) --}}
                    <div x-show="visibleCount === 0" style="display: none;">
                        <x-directory-empty-state />
                    </div>
                </section>
            @else
                {{-- Server-Side Empty State (Figma Node 34:1067) --}}
                <x-directory-empty-state
                    :searchQuery="request('search')"
                    :selectedClass="request('class')"
                />
            @endif

            {{-- ── 4. CENTERED PAGINATION BAR ── --}}
            @if ($students->hasPages())
                <nav
                    aria-label="Navigasi halaman direktori"
                    class="mt-10 pt-6 border-t border-surface-border flex flex-col sm:flex-row items-center justify-between gap-4"
                >
                    <p class="font-mono text-xs uppercase tracking-[0.05em] text-primary-500">
                        HALAMAN {{ $students->currentPage() }} DARI {{ $students->lastPage() }}
                    </p>

                    <div class="flex flex-wrap items-center justify-center gap-2">
                        {{-- Previous Page Link --}}
                        @if ($students->onFirstPage())
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-surface-border bg-surface-subtle font-mono text-xs text-primary-500 cursor-not-allowed select-none">
                                ← Sebelumnya
                            </span>
                        @else
                            <a
                                href="{{ $students-> withQueryString()->previousPageUrl() }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-surface-border bg-white font-mono text-xs font-semibold text-primary-900 hover:bg-surface-card transition-colors duration-180 focus-visible:outline-2 focus-visible:outline-secondary-500"
                            >
                                ← Sebelumnya
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @foreach ($students->getUrlRange(max(1, $students->currentPage() - 2), min($students->lastPage(), $students->currentPage() + 2)) as $page => $url)
                            @if ($page == $students->currentPage())
                                <span
                                    aria-current="page"
                                    class="inline-flex size-9 items-center justify-center rounded-lg bg-secondary-500 border border-surface-border font-mono text-xs font-bold text-primary-900 shadow-2xs"
                                >
                                    {{ $page }}
                                </span>
                            @else
                                <a
                                    href="{{ $students->withQueryString()->url($page) }}"
                                    class="inline-flex size-9 items-center justify-center rounded-lg bg-surface-card border border-surface-border font-mono text-xs font-medium text-primary-700 hover:bg-white hover:text-primary-900 transition-colors duration-180 focus-visible:outline-2 focus-visible:outline-secondary-500"
                                >
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($students->hasMorePages())
                            <a
                                href="{{ $students->withQueryString()->nextPageUrl() }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-surface-border bg-white font-mono text-xs font-semibold text-primary-900 hover:bg-surface-card transition-colors duration-180 focus-visible:outline-2 focus-visible:outline-secondary-500"
                            >
                                Selanjutnya →
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-surface-border bg-surface-subtle font-mono text-xs text-primary-500 cursor-not-allowed select-none">
                                Selanjutnya →
                            </span>
                        @endif
                    </div>
                </nav>
            @endif

        </main>

        {{-- Global Footer --}}
        <x-footer activePage="unlock" />

        @livewireScripts
    </body>
</html>
