@props([
    'colonyGraph' => [],
    'activeAspects' => [],
])

@php
    $center = $colonyGraph['center'] ?? [
        'label' => 'TEKKOM 26',
        'x' => 500,
        'y' => 280,
        'mappedCount' => 0,
        'unmappedCount' => 0,
        'matchedCount' => 0,
    ];
    $aspectNodes = $colonyGraph['aspectNodes'] ?? [];
    $studentNodes = $colonyGraph['studentNodes'] ?? [];
    $studentLookup = collect($studentNodes)
        ->mapWithKeys(fn (array $student): array => [
            $student['nim'] => [
                'nim' => $student['nim'],
                'name' => $student['name'],
                'class_name' => $student['class_name'],
                'mbti_code' => $student['mbti_code'],
                'aspects' => $student['aspects'],
                'matched' => $student['matched'],
            ],
        ])
        ->all();

    $aspectPairs = [
        ['N', 'S'],
        ['T', 'F'],
        ['J', 'P'],
        ['E', 'I'],
    ];

    $axisLabels = [
        ['left' => 'E', 'leftName' => 'Extravert', 'right' => 'I', 'rightName' => 'Introvert', 'axis' => 'Energy'],
        ['left' => 'N', 'leftName' => 'Intuition', 'right' => 'S', 'rightName' => 'Sensing', 'axis' => 'Mind'],
        ['left' => 'T', 'leftName' => 'Thinking', 'right' => 'F', 'rightName' => 'Feeling', 'axis' => 'Nature'],
        ['left' => 'J', 'leftName' => 'Judging', 'right' => 'P', 'rightName' => 'Perceiving', 'axis' => 'Tactics'],
    ];
@endphp

<section
    aria-label="Colony Graph filter kepribadian MBTI 8 aspek"
    class="mb-8 rounded-2xl border border-white/10 bg-surface-card/85 p-4 sm:p-6 relative overflow-hidden glow-gold-subtle"
    x-data="{
        showGraph: true,
        hoveredNim: null,
        hoveredAspect: null,
        hoveredAspects: [],
        hoveredStudent: null,
        activeAspects: @js(array_values($activeAspects)),
        studentsMap: @js($studentLookup),
        hoverStudent(nim) {
            const student = this.studentsMap[nim] || null;
            this.hoveredNim = student ? nim : null;
            this.hoveredStudent = student;
            this.hoveredAspects = student ? (student.aspects || []) : [];
        },
        clearStudent() {
            this.hoveredNim = null;
            this.hoveredStudent = null;
            this.hoveredAspects = [];
        },
        lineStyle(nim) {
            const student = this.studentsMap[nim];
            if (!student) return 'opacity: 0.11;';
            if (this.hoveredNim !== null) {
                return this.hoveredNim === nim
                    ? 'opacity: 0.92; stroke-width: 1.75; stroke: var(--color-brand-gold-glow);'
                    : 'opacity: 0.035; stroke-width: 0.8; stroke: var(--color-text-muted);';
            }
            if (this.hoveredAspect !== null) {
                return (student.aspects || []).includes(this.hoveredAspect)
                    ? 'opacity: 0.45; stroke-width: 1.25; stroke: var(--color-brand-gold);'
                    : 'opacity: 0.04; stroke-width: 0.8; stroke: var(--color-text-muted);';
            }
            if (this.activeAspects.length > 0) {
                return student.matched
                    ? 'opacity: 0.5; stroke-width: 1.25; stroke: var(--color-brand-gold);'
                    : 'opacity: 0.035; stroke-width: 0.8; stroke: var(--color-text-muted);';
            }
            return 'opacity: 0.11; stroke-width: 0.9; stroke: var(--color-brand-gold);';
        },
        dotOpacity(nim) {
            const student = this.studentsMap[nim];
            if (!student) return 0.5;
            if (this.hoveredNim !== null) {
                return this.hoveredNim === nim ? 1 : 0.22;
            }
            if (this.hoveredAspect !== null) {
                return (student.aspects || []).includes(this.hoveredAspect) ? 0.95 : 0.22;
            }
            if (this.activeAspects.length > 0) {
                return student.matched ? 0.95 : 0.18;
            }
            return 0.62;
        },
        isAspectHighlighted(letter) {
            return this.hoveredAspect === letter || this.hoveredAspects.includes(letter);
        }
    }"
>
    {{-- Header & Status Bar --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-white/10">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                <span class="font-display text-[11px] font-semibold uppercase tracking-widest text-brand-gold">
                    Colony Graph &middot; MBTI Indexing
                </span>
                @if ($activeAspects !== [])
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-brand-red/85 border border-brand-red-hover font-mono text-[11px] font-bold text-text-primary tracking-wider">
                        {{ implode(' · ', $activeAspects) }}
                    </span>
                @endif
            </div>
            <h2 class="font-display text-lg sm:text-xl font-bold text-text-primary tracking-tight">
                Peta Konstelasi 8 Aspek Kepribadian
            </h2>
            <p class="mt-1 font-body text-xs sm:text-sm text-text-secondary max-w-2xl">
                Klik aspek untuk mengaktifkan atau switch kutub (<span class="font-mono text-brand-gold">E/I · N/S · T/F · J/P</span>). Arahkan kursor ke titik mahasiswa untuk menyalakan 4 koneksinya.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-canvas-base border border-white/10 font-mono text-xs text-brand-gold">
                <span class="size-2 rounded-full bg-brand-gold" aria-hidden="true"></span>
                <span>{{ $center['mappedCount'] }} Terpetakan</span>
            </span>

            @if (($center['unmappedCount'] ?? 0) > 0)
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl bg-canvas-base border border-white/10 font-mono text-xs text-text-muted">
                    {{ $center['unmappedCount'] }} Belum Mengisi
                </span>
            @endif

            @if ($activeAspects !== [])
                <button
                    type="button"
                    wire:click="resetAspects"
                    class="inline-flex items-center min-h-9 px-3 py-1.5 rounded-xl bg-brand-red/20 hover:bg-brand-red/40 border border-brand-red-hover/60 font-display text-xs font-semibold text-text-primary transition-colors cursor-pointer"
                >
                    Reset Aspek
                </button>
            @endif

            <button
                type="button"
                @click="showGraph = !showGraph"
                class="sm:hidden inline-flex items-center min-h-9 px-3 py-1.5 rounded-xl bg-surface-elevated border border-white/15 font-display text-xs font-semibold text-text-secondary hover:text-brand-gold transition-colors cursor-pointer"
                :aria-expanded="showGraph.toString()"
            >
                <span x-text="showGraph ? 'Sembunyikan Graf' : 'Tampilkan Graf'">Sembunyikan Graf</span>
            </button>
        </div>
    </div>

    {{-- Interactive Constellation SVG Canvas --}}
    <div
        x-show="showGraph"
        x-transition.opacity
        class="relative mt-4 rounded-xl border border-white/10 bg-canvas-base/95 overflow-hidden"
    >
        <div class="bg-hex-pattern absolute inset-0 opacity-60 pointer-events-none" aria-hidden="true"></div>

        {{-- Floating Student Hover Tooltip --}}
        <div
            x-cloak
            x-show="hoveredStudent !== null"
            x-transition.opacity.duration.150ms
            class="pointer-events-none absolute top-3 left-3 right-3 sm:right-auto sm:w-72 z-20 rounded-xl border border-brand-gold/50 bg-surface-card/95 backdrop-blur-xs p-3 shadow-lg"
        >
            <template x-if="hoveredStudent">
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-mono text-xs font-bold px-2 py-0.5 rounded bg-brand-red text-text-primary border border-brand-red-hover" x-text="hoveredStudent.mbti_code"></span>
                        <span class="font-mono text-[11px] text-brand-gold" x-text="'Kelas ' + hoveredStudent.class_name"></span>
                    </div>
                    <p class="font-display text-sm font-bold text-text-primary leading-snug line-clamp-1" x-text="hoveredStudent.name"></p>
                    <p class="font-mono text-[11px] text-text-secondary" x-text="hoveredStudent.nim"></p>
                    <p class="font-body text-[11px] text-brand-gold/90">Klik titik node untuk membuka profil &rarr;</p>
                </div>
            </template>
        </div>

        <svg
            viewBox="0 0 1000 560"
            class="relative z-10 w-full h-auto max-h-[430px] select-none"
            role="img"
            aria-label="Graf konstelasi mahasiswa Teknik Komputer 2026 ke 8 aspek kepribadian MBTI"
            @mouseleave="clearStudent(); hoveredAspect = null"
        >
            {{-- Subtle Orbital Ring Guides & Polar Axes --}}
            <g class="pointer-events-none">
                <ellipse cx="500" cy="280" rx="422" ry="235" fill="none" class="stroke-white/10" stroke-width="1" stroke-dasharray="4 6" />
                <ellipse cx="500" cy="280" rx="352" ry="196" fill="none" class="stroke-white/10" stroke-width="1" stroke-dasharray="2 6" />
                <ellipse cx="500" cy="280" rx="185" ry="138" fill="none" class="stroke-brand-gold/20" stroke-width="1" />

                {{-- Opposing Axis Bridges Through Center --}}
                @foreach ($aspectPairs as [$poleA, $poleB])
                    @if (isset($aspectNodes[$poleA], $aspectNodes[$poleB]))
                        <line
                            x1="{{ $aspectNodes[$poleA]['x'] }}"
                            y1="{{ $aspectNodes[$poleA]['y'] }}"
                            x2="{{ $aspectNodes[$poleB]['x'] }}"
                            y2="{{ $aspectNodes[$poleB]['y'] }}"
                            class="stroke-white/15"
                            stroke-width="1"
                            stroke-dasharray="3 5"
                        />
                    @endif
                @endforeach
            </g>

            {{-- Student-to-Aspect Connection Edges + Outer Student Nodes --}}
            <g>
                @foreach ($studentNodes as $student)
                    @php $nimKey = $student['nim']; @endphp
                    <g
                        data-student-node="{{ $nimKey }}"
                        class="cursor-pointer"
                        @mouseenter="hoverStudent('{{ $nimKey }}')"
                        @mouseleave="clearStudent()"
                    >
                        {{-- 4 Connection Lines to Student MBTI Aspects (dimmed idle, bright on hover/filter) --}}
                        @foreach ($student['aspects'] as $letter)
                            @if (isset($aspectNodes[$letter]))
                                <line
                                    x1="{{ $student['x'] }}"
                                    y1="{{ $student['y'] }}"
                                    x2="{{ $aspectNodes[$letter]['x'] }}"
                                    y2="{{ $aspectNodes[$letter]['y'] }}"
                                    class="pointer-events-none transition-opacity duration-150"
                                    :style="lineStyle('{{ $nimKey }}')"
                                />
                            @endif
                        @endforeach

                        {{-- Clickable Student Node --}}
                        <a href="{{ $student['url'] }}" aria-label="{{ $student['name'] }} ({{ $student['mbti_code'] }})">
                            <circle
                                cx="{{ $student['x'] }}"
                                cy="{{ $student['y'] }}"
                                r="{{ $student['matched'] && $activeAspects !== [] ? 5.5 : 4.2 }}"
                                stroke-width="1"
                                :r="hoveredNim === '{{ $nimKey }}' ? 7.5 : ({{ $student['matched'] && $activeAspects !== [] ? 'true' : 'false' }} ? 5.5 : 4.2)"
                                :class="hoveredNim === '{{ $nimKey }}' ? 'fill-brand-gold-glow stroke-brand-red-hover' : ({{ $student['matched'] && $activeAspects !== [] ? 'true' : 'false' }} ? 'fill-brand-gold stroke-canvas-base' : 'fill-text-secondary stroke-canvas-base')"
                                :style="'opacity: ' + dotOpacity('{{ $nimKey }}')"
                            />
                        </a>
                    </g>
                @endforeach
            </g>

            {{-- Center Node: TEKKOM 26 --}}
            <g class="pointer-events-none">
                <circle cx="500" cy="280" r="52" class="fill-canvas-base stroke-brand-gold/45" stroke-width="1.5" />
                <circle cx="500" cy="280" r="44" class="fill-brand-red stroke-brand-gold" stroke-width="1.5" />
                <text
                    x="500"
                    y="274"
                    text-anchor="middle"
                    class="fill-text-primary font-display text-[13px] font-bold tracking-wider"
                >
                    TEKKOM 26
                </text>
                <text
                    x="500"
                    y="293"
                    text-anchor="middle"
                    class="fill-brand-gold font-mono text-[10px] font-semibold"
                >
                    @if ($activeAspects !== [])
                        {{ $center['matchedCount'] }} MATCH
                    @else
                        {{ $center['mappedCount'] }} NODES
                    @endif
                </text>
            </g>

            {{-- Ring 1: 8 Interactive Aspect Hub Nodes (E, I, N, S, T, F, J, P) --}}
            @foreach ($aspectNodes as $letter => $node)
                @php $isActive = $node['active']; @endphp
                <g
                    role="button"
                    tabindex="0"
                    data-aspect-node="{{ $letter }}"
                    aria-label="Filter aspek {{ $letter }} ({{ $node['label'] }}) — {{ $node['count'] }} mahasiswa"
                    aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                    wire:click="toggleAspect('{{ $letter }}')"
                    @keydown.enter.prevent="$wire.toggleAspect('{{ $letter }}')"
                    @keydown.space.prevent="$wire.toggleAspect('{{ $letter }}')"
                    @mouseenter="hoveredAspect = '{{ $letter }}'"
                    @mouseleave="hoveredAspect = null"
                    class="cursor-pointer focus:outline-none group"
                >
                    <circle
                        cx="{{ $node['x'] }}"
                        cy="{{ $node['y'] }}"
                        r="31"
                        class="transition-colors duration-150 {{ $isActive ? 'fill-brand-red stroke-brand-gold-glow' : 'fill-surface-elevated stroke-brand-gold/50 group-hover:stroke-brand-gold' }}"
                        stroke-width="{{ $isActive ? '2.5' : '1.5' }}"
                        :class="isAspectHighlighted('{{ $letter }}') ? 'stroke-brand-gold-glow fill-surface-card' : ({{ $isActive ? 'true' : 'false' }} ? 'fill-brand-red stroke-brand-gold-glow' : 'fill-surface-elevated stroke-brand-gold/45')"
                    />
                    <text
                        x="{{ $node['x'] }}"
                        y="{{ $node['y'] - 3 }}"
                        text-anchor="middle"
                        class="font-display text-[16px] font-bold {{ $isActive ? 'fill-text-primary' : 'fill-brand-gold' }}"
                    >
                        {{ $letter }}
                    </text>
                    <text
                        x="{{ $node['x'] }}"
                        y="{{ $node['y'] + 12 }}"
                        text-anchor="middle"
                        class="font-mono text-[9.5px] font-semibold {{ $isActive ? 'fill-brand-gold-glow' : 'fill-text-secondary' }}"
                    >
                        {{ $node['count'] }}
                    </text>
                </g>
            @endforeach
        </svg>
    </div>

    {{-- Accessible 4-Axis Toggle Bar (Always usable cleanly at 390px mobile & keyboard) --}}
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach ($axisLabels as $pair)
            @php
                $left = $pair['left'];
                $right = $pair['right'];
                $leftNode = $aspectNodes[$left] ?? ['active' => false, 'count' => 0];
                $rightNode = $aspectNodes[$right] ?? ['active' => false, 'count' => 0];
            @endphp
            <div class="rounded-xl border border-white/10 bg-canvas-base/80 p-2 flex flex-col gap-1.5">
                <span class="px-1.5 font-mono text-[10px] uppercase tracking-wider text-text-muted">
                    {{ $pair['axis'] }} &middot; {{ $left }}/{{ $right }}
                </span>
                <div class="grid grid-cols-2 gap-1.5">
                    <button
                        type="button"
                        wire:click="toggleAspect('{{ $left }}')"
                        aria-pressed="{{ $leftNode['active'] ? 'true' : 'false' }}"
                        data-aspect-pill="{{ $left }}"
                        @class([
                            'min-h-10 px-2.5 py-1.5 rounded-lg border font-display text-xs font-semibold transition-colors cursor-pointer flex items-center justify-between gap-1',
                            'bg-brand-red text-text-primary border-brand-gold/70' => $leftNode['active'],
                            'bg-surface-elevated text-text-secondary border-white/10 hover:border-brand-gold/45 hover:text-text-primary' => ! $leftNode['active'],
                        ])
                    >
                        <span class="truncate">{{ $left }} · {{ $pair['leftName'] }}</span>
                        <span class="font-mono text-[11px] {{ $leftNode['active'] ? 'text-brand-gold-glow' : 'text-brand-gold' }}">{{ $leftNode['count'] }}</span>
                    </button>

                    <button
                        type="button"
                        wire:click="toggleAspect('{{ $right }}')"
                        aria-pressed="{{ $rightNode['active'] ? 'true' : 'false' }}"
                        data-aspect-pill="{{ $right }}"
                        @class([
                            'min-h-10 px-2.5 py-1.5 rounded-lg border font-display text-xs font-semibold transition-colors cursor-pointer flex items-center justify-between gap-1',
                            'bg-brand-red text-text-primary border-brand-gold/70' => $rightNode['active'],
                            'bg-surface-elevated text-text-secondary border-white/10 hover:border-brand-gold/45 hover:text-text-primary' => ! $rightNode['active'],
                        ])
                    >
                        <span class="truncate">{{ $right }} · {{ $pair['rightName'] }}</span>
                        <span class="font-mono text-[11px] {{ $rightNode['active'] ? 'text-brand-gold-glow' : 'text-brand-gold' }}">{{ $rightNode['count'] }}</span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</section>
