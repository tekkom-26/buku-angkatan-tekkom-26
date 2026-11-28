<x-layouts.app activePage="graph" :title="'Graph View MBTI — ' . ($cohortName ?? 'COMETS 2026')" :withFooter="false" :lockViewport="true">
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
        class="relative flex-1 min-h-0 w-full overflow-hidden overscroll-none select-none"
        x-data="{
            graph: null,
            payload: @js($graphPayload),
            activeAspects: @js($graphPayload['activeAspects'] ?? []),
            searchQuery: '',
            matchedCount: {{ (int) ($center['mappedCount'] ?? 0) }},
            aspectCounts: @js(collect($graphPayload['aspectNodes'] ?? [])->mapWithKeys(fn ($n, $k) => [$k => $n['count']])->all()),
            selectedStudent: null,
            isDesktop: window.innerWidth >= 768,
            mobileTab: null,
            showDesktopControls: window.innerWidth >= 1024,
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
                            if (student && !this.isDesktop) {
                                this.mobileTab = null;
                            }
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

            toggleMobileTab(tab) {
                this.mobileTab = this.mobileTab === tab ? null : tab;
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
        @resize.window="isDesktop = window.innerWidth >= 768"
        @keydown.escape.window="clearSelection(); mobileTab = null"
    >
        {{-- Interactive Force-Directed HTML5 Canvas --}}
        <canvas
            x-ref="canvas"
            class="absolute inset-0 w-full h-full touch-none block cursor-grab"
            aria-label="Kanvas interaktif Graph View MBTI Tekkom 26"
        ></canvas>

        {{-- Unified Top-Left HUD (Desktop: Filters card; Mobile: Single unified bar + mutually exclusive Filter / Controls tabs) --}}
        <div class="pointer-events-none absolute top-3 inset-x-3 md:right-auto md:w-88 z-20 flex flex-col gap-2">
            <div class="pointer-events-auto rounded-2xl border border-white/15 bg-surface-card/92 backdrop-blur-md p-3 sm:p-3.5 shadow-xl space-y-2.5">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="font-display text-[10px] sm:text-[11px] font-semibold uppercase tracking-widest text-brand-gold truncate">
                            Fun Corner &middot; Obsidian Graph
                        </p>
                        <h1 class="font-display text-sm sm:text-lg font-bold text-text-primary leading-snug truncate">
                            Konstelasi MBTI Tekkom 26
                        </h1>
                    </div>

                    {{-- Mobile Exclusive Tab Buttons (Filter / Controls) --}}
                    <div class="flex md:hidden items-center gap-1.5 shrink-0">
                        <button
                            type="button"
                            @click="toggleMobileTab('filter')"
                            :aria-expanded="(mobileTab === 'filter').toString()"
                            :class="mobileTab === 'filter' || activeAspects.length > 0 ? 'bg-brand-red text-text-primary border-brand-gold/60' : 'bg-surface-elevated text-text-secondary border-white/15'"
                            class="min-h-9 px-2.5 py-1 rounded-lg border font-display text-[11px] font-semibold transition-colors cursor-pointer inline-flex items-center gap-1"
                        >
                            <span>Filter</span>
                            <span
                                x-show="activeAspects.length > 0"
                                x-text="activeAspects.length"
                                class="px-1 rounded bg-canvas-base/70 font-mono text-[10px] text-brand-gold"
                            ></span>
                        </button>
                        <button
                            type="button"
                            @click="toggleMobileTab('controls')"
                            :aria-expanded="(mobileTab === 'controls').toString()"
                            :class="mobileTab === 'controls' ? 'bg-brand-red text-text-primary border-brand-gold/60' : 'bg-surface-elevated text-brand-gold border-white/15'"
                            class="min-h-9 px-2.5 py-1 rounded-lg border font-display text-[11px] font-semibold transition-colors cursor-pointer inline-flex items-center gap-1"
                        >
                            <svg class="size-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                            <span>Controls</span>
                        </button>
                    </div>
                </div>

                {{-- Search Node Input --}}
                <div class="relative flex items-center gap-2">
                    <div class="relative flex-1 min-w-0">
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
                    <span class="md:hidden shrink-0 font-mono text-[11px] text-brand-gold px-2 py-1 rounded-lg bg-canvas-base border border-white/10">
                        <span x-text="matchedCount">{{ (int) ($center['mappedCount'] ?? 0) }}</span>
                    </span>
                </div>

                {{-- 8-Aspect Filter Drawer (Always open on Desktop md+; toggled via Filter pill on Mobile) --}}
                <div
                    x-show="isDesktop || mobileTab === 'filter'"
                    x-transition
                    class="space-y-2.5 max-h-[50dvh] overflow-y-auto overscroll-contain md:max-h-none md:overflow-visible pt-1"
                >
                    <div class="grid grid-cols-2 gap-1.5">
                        @foreach ($axes as $pair)
                            <div class="flex rounded-lg border border-white/10 bg-canvas-base/85 p-0.5 gap-0.5">
                                <button
                                    type="button"
                                    @click="toggleAspect('{{ $pair['left'] }}')"
                                    :aria-pressed="activeAspects.includes('{{ $pair['left'] }}').toString()"
                                    :class="activeAspects.includes('{{ $pair['left'] }}') ? 'bg-brand-red text-text-primary border-brand-gold/60' : 'text-text-secondary hover:text-brand-gold border-transparent'"
                                    class="flex-1 min-h-10 md:min-h-8 px-2 py-1 rounded-md border font-mono text-[11px] font-bold transition-colors cursor-pointer flex items-center justify-between"
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
                                    class="flex-1 min-h-10 md:min-h-8 px-2 py-1 rounded-md border font-mono text-[11px] font-bold transition-colors cursor-pointer flex items-center justify-between"
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
                                class="min-h-9 md:min-h-8 px-2.5 py-1 rounded-lg border border-white/15 bg-surface-elevated font-display text-[11px] font-semibold text-text-secondary hover:text-brand-gold cursor-pointer"
                            >
                                Reset
                            </button>

                            <a
                                :href="directoryUrl()"
                                href="{{ route('directory.index') }}"
                                class="inline-flex items-center gap-1 min-h-9 md:min-h-8 px-3 py-1 rounded-lg bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover font-display text-[11px] font-semibold text-text-primary transition-colors"
                            >
                                <span>Lihat di Direktori</span>
                                <span class="text-brand-gold" aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Mobile-Only In-Flow Graph Controls Drawer (zero overlap with filter or canvas chrome) --}}
                <div
                    x-cloak
                    x-show="!isDesktop && mobileTab === 'controls'"
                    x-transition
                    class="md:hidden pt-2 border-t border-white/10 max-h-[50dvh] overflow-y-auto overscroll-contain"
                >
                    <x-graph-controls-body />
                </div>
            </div>
        </div>

        {{-- Desktop-Only Top-Right HUD: Obsidian-Style Graph Controls --}}
        <div class="pointer-events-none hidden md:flex md:absolute md:top-3 md:right-3 z-20 flex-col items-end gap-2">
            <button
                type="button"
                @click="showDesktopControls = !showDesktopControls"
                class="pointer-events-auto inline-flex items-center gap-2 min-h-9 px-3 py-1.5 rounded-xl border border-white/15 bg-surface-card/90 backdrop-blur-md font-display text-xs font-semibold text-brand-gold hover:border-brand-gold/50 shadow-lg cursor-pointer"
                :aria-expanded="showDesktopControls.toString()"
            >
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                </svg>
                <span>Graph Controls</span>
            </button>

            <div
                x-cloak
                x-show="showDesktopControls"
                x-transition
                class="pointer-events-auto w-64 rounded-2xl border border-white/15 bg-surface-card/92 backdrop-blur-md p-4 shadow-xl"
            >
                <x-graph-controls-body />
            </div>
        </div>

        {{-- Mobile/Tablet Always-Visible Back to Directory Button (bottom-left, hidden when Mini Card opens or on md+ where Filter HUD is open) --}}
        <div
            :class="selectedStudent ? 'hidden' : 'flex md:hidden'"
            class="pointer-events-none absolute bottom-4 left-3 z-20 pb-[env(safe-area-inset-bottom)]"
        >
            <a
                :href="directoryUrl()"
                href="{{ route('directory.index') }}"
                class="pointer-events-auto inline-flex items-center gap-1.5 min-h-11 px-3.5 py-2 rounded-xl bg-surface-card/92 hover:bg-brand-red border border-white/15 hover:border-brand-red-hover backdrop-blur-md font-display text-xs font-semibold text-text-primary shadow-lg transition-colors"
                aria-label="Kembali ke Direktori Mahasiswa"
            >
                <span class="font-mono text-brand-gold" aria-hidden="true">&larr;</span>
                <span>Direktori</span>
            </a>
        </div>

        {{-- Bottom Zoom Controls (Mobile/Tablet: bottom-right & auto-hidden when Mini Profile Card opens; Desktop: bottom-left) --}}
        <div
            :class="selectedStudent ? 'hidden md:flex' : 'flex'"
            class="pointer-events-none absolute bottom-4 right-3 md:right-auto md:left-3 z-20 flex-col gap-2 pb-[env(safe-area-inset-bottom)]"
        >
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
                <span>Drag node untuk menarik pegas &middot; Scroll / pinch zoom &middot; Klik node mahasiswa untuk Mini Profil</span>
            </div>
        </div>

        {{-- Bottom-Right HUD: Corner Mini Profile Card (when student node clicked) --}}
        <div
            x-cloak
            x-show="selectedStudent !== null"
            x-transition
            id="graph-mini-profile-card"
            class="pointer-events-auto absolute bottom-4 right-3 left-3 sm:left-auto sm:w-84 z-30 rounded-2xl border border-brand-gold/55 bg-surface-card/95 backdrop-blur-md p-4 shadow-2xl mb-[env(safe-area-inset-bottom)]"
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
