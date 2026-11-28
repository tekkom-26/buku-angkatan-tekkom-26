<x-layouts.app activePage="graph" :title="'Graph View MBTI — ' . ($cohortName ?? 'COMETS 2026')" :withFooter="false">
    @php
        $center = $graphPayload['center'] ?? [];
        $axes = [
            ['left' => 'E', 'leftName' => 'Extravert', 'right' => 'I', 'rightName' => 'Introvert'],
            ['left' => 'N', 'leftName' => 'Intuition', 'right' => 'S', 'rightName' => 'Sensing'],
            ['left' => 'T', 'leftName' => 'Thinking', 'right' => 'F', 'rightName' => 'Feeling'],
            ['left' => 'J', 'leftName' => 'Judging', 'right' => 'P', 'rightName' => 'Perceiving'],
        ];
    @endphp

    <main
        id="main-content"
        class="relative grow w-full h-[calc(100vh-5rem)] min-h-[600px] overflow-hidden select-none"
        x-data="{
            graph: null,
            payload: @js($graphPayload),
            activeAspects: @js($graphPayload['activeAspects'] ?? []),
            searchQuery: '',
            matchedCount: {{ (int) ($center['mappedCount'] ?? 0) }},
            aspectCounts: @js(collect($graphPayload['aspectNodes'] ?? [])->mapWithKeys(fn ($n, $k) => [$k => $n['count']])->all()),
            selectedStudent: null,
            showFilters: window.innerWidth >= 768,
            showControls: window.innerWidth >= 1024,
            repelStrength: 135,
            linkDistance: 145,
            showLinks: true,
            showLabels: false,

            init() {
                const mountGraph = () => {
                    if (!window.mbtiGraphRuntime || !this.$refs.canvas) {
                        return false;
                    }
                    this.graph = window.mbtiGraphRuntime.create(this.$refs.canvas, this.payload, {
                        onSelectStudent: (student) => {
                            this.selectedStudent = student;
                        },
                        onToggleAspect: (letter) => {
                            this.toggleAspect(letter);
                        },
                        onStatsChange: ({ matchedCount, aspectCounts }) => {
                            this.matchedCount = matchedCount;
                            this.aspectCounts = aspectCounts;
                        },
                    });
                    return true;
                };

                if (!mountGraph()) {
                    window.addEventListener('load', () => mountGraph(), { once: true });
                }

                this.$watch('searchQuery', (val) => {
                    if (this.graph) this.graph.setSearchQuery(val);
                });
                this.$watch('repelStrength', (val) => {
                    if (this.graph) this.graph.setRepelStrength(val);
                });
                this.$watch('linkDistance', (val) => {
                    if (this.graph) this.graph.setLinkDistance(val);
                });
                this.$watch('showLinks', (val) => {
                    if (this.graph) this.graph.setShowLinks(val);
                });
                this.$watch('showLabels', (val) => {
                    if (this.graph) this.graph.setShowStudentLabels(val);
                });
            },

            toggleAspect(letter) {
                if (window.mbtiGraphRuntime) {
                    this.activeAspects = window.mbtiGraphRuntime.toggleAspect(this.activeAspects, letter);
                } else {
                    const idx = this.activeAspects.indexOf(letter);
                    this.activeAspects = idx >= 0
                        ? this.activeAspects.filter((a) => a !== letter)
                        : [...this.activeAspects, letter];
                }

                if (this.graph) {
                    this.graph.setActiveAspects(this.activeAspects);
                }

                const url = new URL(window.location.href);
                if (this.activeAspects.length > 0) {
                    url.searchParams.set('aspects', this.activeAspects.join(','));
                } else {
                    url.searchParams.delete('aspects');
                }
                window.history.replaceState({}, '', url);
            },

            resetFilters() {
                this.activeAspects = [];
                this.searchQuery = '';
                if (this.graph) {
                    this.graph.setActiveAspects([]);
                    this.graph.setSearchQuery('');
                }
                const url = new URL(window.location.href);
                url.searchParams.delete('aspects');
                window.history.replaceState({}, '', url);
            },

            clearSelection() {
                this.selectedStudent = null;
                if (this.graph) this.graph.clearSelection();
            },

            directoryUrl() {
                const base = this.payload.directoryUrl || '/directory';
                if (this.activeAspects.length === 0 && this.searchQuery.trim() === '') {
                    return base;
                }
                const params = new URLSearchParams();
                if (this.activeAspects.length > 0) {
                    params.set('aspects', this.activeAspects.join(','));
                }
                if (this.searchQuery.trim() !== '') {
                    params.set('search', this.searchQuery.trim());
                }
                return `${base}?${params.toString()}`;
            }
        }"
        @keydown.escape.window="clearSelection()"
    >
        {{-- Interactive Force-Directed HTML5 Canvas --}}
        <canvas
            x-ref="canvas"
            class="absolute inset-0 w-full h-full touch-none block cursor-grab"
            aria-label="Kanvas interaktif Graph View MBTI Tekkom 26"
        ></canvas>

        {{-- Top-Left HUD: Title, Live Search, Aspect Filters & Bridge CTA --}}
        <div class="pointer-events-none absolute top-3 left-3 right-3 md:right-auto md:w-88 z-20 flex flex-col gap-2.5">
            <div class="pointer-events-auto rounded-2xl border border-white/15 bg-surface-card/90 backdrop-blur-md p-3.5 shadow-xl space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="font-display text-[11px] font-semibold uppercase tracking-widest text-brand-gold">
                            Fun Corner &middot; Obsidian Graph View
                        </p>
                        <h1 class="font-display text-base sm:text-lg font-bold text-text-primary leading-snug">
                            Konstelasi MBTI Tekkom 26
                        </h1>
                    </div>
                    <button
                        type="button"
                        @click="showFilters = !showFilters"
                        class="md:hidden shrink-0 min-h-8 px-2.5 py-1 rounded-lg bg-surface-elevated border border-white/15 font-display text-[11px] font-semibold text-brand-gold cursor-pointer"
                    >
                        <span x-text="showFilters ? 'Tutup' : 'Filter'">Filter</span>
                    </button>
                </div>

                {{-- Search Node Input --}}
                <div class="relative">
                    <label for="graph-node-search" class="sr-only">Cari mahasiswa atau tipe MBTI di graf</label>
                    <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 size-3.5 text-text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-3.5-3.5" />
                    </svg>
                    <input
                        id="graph-node-search"
                        type="search"
                        x-model="searchQuery"
                        placeholder="Highlight nama, NIM, atau ENTJ..."
                        class="w-full min-h-9 rounded-xl border border-white/15 bg-canvas-base/90 pl-8 pr-3 py-1.5 font-body text-xs text-text-primary placeholder:text-text-muted focus:border-brand-gold focus:outline-none"
                    />
                </div>

                {{-- Collapsible 8-Aspect Filter Pills --}}
                <div x-show="showFilters" x-transition class="space-y-2">
                    <div class="grid grid-cols-2 gap-1.5">
                        @foreach ($axes as $pair)
                            <div class="flex rounded-lg border border-white/10 bg-canvas-base/85 p-0.5 gap-0.5">
                                <button
                                    type="button"
                                    @click="toggleAspect('{{ $pair['left'] }}')"
                                    :aria-pressed="activeAspects.includes('{{ $pair['left'] }}').toString()"
                                    :class="activeAspects.includes('{{ $pair['left'] }}') ? 'bg-brand-red text-text-primary border-brand-gold/60' : 'text-text-secondary hover:text-brand-gold border-transparent'"
                                    class="flex-1 min-h-8 px-2 py-1 rounded-md border font-mono text-[11px] font-bold transition-colors cursor-pointer flex items-center justify-between"
                                    title="{{ $pair['leftName'] }}"
                                >
                                    <span>{{ $pair['left'] }}</span>
                                    <span class="text-[10px] opacity-85" x-text="aspectCounts['{{ $pair['left'] }}'] ?? 0"></span>
                                </button>
                                <button
                                    type="button"
                                    @click="toggleAspect('{{ $pair['right'] }}')"
                                    :aria-pressed="activeAspects.includes('{{ $pair['right'] }}').toString()"
                                    :class="activeAspects.includes('{{ $pair['right'] }}') ? 'bg-brand-red text-text-primary border-brand-gold/60' : 'text-text-secondary hover:text-brand-gold border-transparent'"
                                    class="flex-1 min-h-8 px-2 py-1 rounded-md border font-mono text-[11px] font-bold transition-colors cursor-pointer flex items-center justify-between"
                                    title="{{ $pair['rightName'] }}"
                                >
                                    <span>{{ $pair['right'] }}</span>
                                    <span class="text-[10px] opacity-85" x-text="aspectCounts['{{ $pair['right'] }}'] ?? 0"></span>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between gap-2 pt-1">
                        <span class="font-mono text-[11px] text-text-secondary">
                            <strong class="text-brand-gold" x-text="matchedCount">{{ (int) ($center['mappedCount'] ?? 0) }}</strong> node cocok
                        </span>

                        <div class="flex items-center gap-1.5">
                            <button
                                x-cloak
                                x-show="activeAspects.length > 0 || searchQuery.trim() !== ''"
                                type="button"
                                @click="resetFilters()"
                                class="min-h-8 px-2.5 py-1 rounded-lg border border-white/15 bg-surface-elevated font-display text-[11px] font-semibold text-text-secondary hover:text-brand-gold cursor-pointer"
                            >
                                Reset
                            </button>

                            <a
                                :href="directoryUrl()"
                                href="{{ route('directory.index') }}"
                                class="inline-flex items-center gap-1 min-h-8 px-2.5 py-1 rounded-lg bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover font-display text-[11px] font-semibold text-text-primary transition-colors"
                            >
                                <span>Lihat di Direktori</span>
                                <span class="text-brand-gold" aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top-Right HUD: Obsidian-Style Graph Controls (Physics & Display) --}}
        <div class="pointer-events-none fixed md:absolute top-20 md:top-3 right-3 z-20 flex flex-col items-end gap-2">
            <button
                type="button"
                @click="showControls = !showControls"
                class="pointer-events-auto inline-flex items-center gap-2 min-h-9 px-3 py-1.5 rounded-xl border border-white/15 bg-surface-card/90 backdrop-blur-md font-display text-xs font-semibold text-brand-gold hover:border-brand-gold/50 shadow-lg cursor-pointer"
                :aria-expanded="showControls.toString()"
            >
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                </svg>
                <span>Graph Controls</span>
            </button>

            <div
                x-cloak
                x-show="showControls"
                x-transition
                class="pointer-events-auto w-64 rounded-2xl border border-white/15 bg-surface-card/92 backdrop-blur-md p-4 shadow-xl space-y-4"
            >
                <div class="space-y-3">
                    <p class="font-display text-[11px] font-semibold uppercase tracking-wider text-brand-gold">
                        Forces
                    </p>

                    <label class="block space-y-1">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-body text-text-secondary">Repel Force</span>
                            <span class="font-mono text-brand-gold" x-text="repelStrength"></span>
                        </div>
                        <input
                            type="range"
                            min="50"
                            max="320"
                            step="5"
                            x-model.number="repelStrength"
                            class="w-full accent-brand-gold cursor-pointer"
                        />
                    </label>

                    <label class="block space-y-1">
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="font-body text-text-secondary">Link Distance</span>
                            <span class="font-mono text-brand-gold" x-text="linkDistance"></span>
                        </div>
                        <input
                            type="range"
                            min="70"
                            max="260"
                            step="5"
                            x-model.number="linkDistance"
                            class="w-full accent-brand-gold cursor-pointer"
                        />
                    </label>
                </div>

                <div class="pt-3 border-t border-white/10 space-y-2.5">
                    <p class="font-display text-[11px] font-semibold uppercase tracking-wider text-brand-gold">
                        Display
                    </p>

                    <label class="flex items-center justify-between gap-2 text-xs text-text-secondary cursor-pointer">
                        <span>Tampilkan Garis Koneksi</span>
                        <input type="checkbox" x-model="showLinks" class="size-4 accent-brand-gold cursor-pointer" />
                    </label>

                    <label class="flex items-center justify-between gap-2 text-xs text-text-secondary cursor-pointer">
                        <span>Label Nama Mahasiswa</span>
                        <input type="checkbox" x-model="showLabels" class="size-4 accent-brand-gold cursor-pointer" />
                    </label>
                </div>

                <div class="pt-3 border-t border-white/10 grid grid-cols-2 gap-2">
                    <button
                        type="button"
                        @click="graph && graph.reheat()"
                        class="min-h-8 px-2.5 py-1.5 rounded-lg bg-surface-elevated border border-white/10 hover:border-brand-gold/50 font-display text-[11px] font-semibold text-text-primary cursor-pointer"
                    >
                        Re-heat
                    </button>
                    <button
                        type="button"
                        @click="graph && graph.resetCamera()"
                        class="min-h-8 px-2.5 py-1.5 rounded-lg bg-surface-elevated border border-white/10 hover:border-brand-gold/50 font-display text-[11px] font-semibold text-brand-gold cursor-pointer"
                    >
                        Reset View
                    </button>
                </div>
            </div>
        </div>

        {{-- Bottom-Left HUD: Zoom Controls & Hint --}}
        <div class="pointer-events-none absolute bottom-3 left-3 z-20 flex flex-col gap-2">
            <div class="pointer-events-auto inline-flex items-center gap-1 rounded-xl border border-white/15 bg-surface-card/90 backdrop-blur-md p-1 shadow-lg w-fit">
                <button
                    type="button"
                    @click="graph && graph.zoomBy(1.2)"
                    class="size-9 rounded-lg bg-surface-elevated hover:border-brand-gold/50 border border-white/10 font-mono text-sm font-bold text-text-primary cursor-pointer"
                    aria-label="Zoom In"
                >+</button>
                <button
                    type="button"
                    @click="graph && graph.zoomBy(0.83)"
                    class="size-9 rounded-lg bg-surface-elevated hover:border-brand-gold/50 border border-white/10 font-mono text-sm font-bold text-text-primary cursor-pointer"
                    aria-label="Zoom Out"
                >&minus;</button>
                <button
                    type="button"
                    @click="graph && graph.resetCamera()"
                    class="min-h-9 px-2.5 rounded-lg bg-surface-elevated hover:border-brand-gold/50 border border-white/10 font-display text-[11px] font-semibold text-brand-gold cursor-pointer"
                >Reset</button>
            </div>

            <div class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-white/10 bg-canvas-base/80 backdrop-blur-xs font-body text-[11px] text-text-muted">
                <span>Drag node untuk menarik pegas &middot; Scroll zoom &middot; Klik node mahasiswa untuk Mini Profil</span>
            </div>
        </div>

        {{-- Bottom-Right HUD: Corner Mini Profile Card (when student node clicked) --}}
        <div
            x-cloak
            x-show="selectedStudent !== null"
            x-transition
            id="graph-mini-profile-card"
            class="pointer-events-auto absolute bottom-3 right-3 left-3 sm:left-auto sm:w-84 z-30 rounded-2xl border border-brand-gold/55 bg-surface-card/95 backdrop-blur-md p-4 shadow-2xl"
        >
            <template x-if="selectedStudent">
                <div class="space-y-3.5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <img
                                :src="selectedStudent.avatar_url"
                                :alt="selectedStudent.name"
                                class="size-12 rounded-xl object-cover border border-white/15 bg-canvas-base shrink-0"
                            />
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-brand-red border border-brand-red-hover text-text-primary" x-text="selectedStudent.mbti_code"></span>
                                    <span class="font-mono text-[11px] text-brand-gold" x-text="'Kelas ' + selectedStudent.class_name"></span>
                                </div>
                                <h2 class="mt-1 font-display text-sm font-bold text-text-primary truncate" x-text="selectedStudent.name"></h2>
                                <p class="font-mono text-[11px] text-text-muted" x-text="selectedStudent.nim"></p>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="clearSelection()"
                            class="size-8 rounded-lg bg-surface-elevated border border-white/10 text-text-muted hover:text-text-primary flex items-center justify-center shrink-0 cursor-pointer"
                            aria-label="Tutup mini profil"
                        >
                            &times;
                        </button>
                    </div>

                    <div class="flex items-center justify-between gap-2 px-3 py-2 rounded-xl bg-canvas-base/85 border border-white/10 text-xs">
                        <span class="font-display font-semibold text-brand-gold" x-text="selectedStudent.mbti_title"></span>
                        <span class="font-mono text-[11px] text-text-secondary" x-text="selectedStudent.temperament_name + ' (' + selectedStudent.temperament_code + ')'"></span>
                    </div>

                    <div class="grid grid-cols-4 gap-1.5 text-center">
                        <template x-for="pair in [['E','I'], ['N','S'], ['T','F'], ['J','P']]" :key="pair[0]">
                            <div class="rounded-lg bg-canvas-base border border-white/10 py-1.5 px-1">
                                <div class="font-mono text-[10px] font-bold text-brand-gold" x-text="(selectedStudent.pcts[pair[0]] >= selectedStudent.pcts[pair[1]] ? pair[0] : pair[1])"></div>
                                <div class="font-mono text-[11px] text-text-primary" x-text="Math.max(selectedStudent.pcts[pair[0]] || 0, selectedStudent.pcts[pair[1]] || 0) + '%'"></div>
                            </div>
                        </template>
                    </div>

                    <a
                        :href="selectedStudent.url"
                        href="#"
                        class="flex items-center justify-between w-full min-h-10 px-4 py-2 rounded-xl bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover font-display text-xs font-semibold text-text-primary transition-colors"
                    >
                        <span>Buka Profil Lengkap</span>
                        <span class="font-mono text-brand-gold" aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </template>
        </div>
    </main>
</x-layouts.app>
