@props([
    'content' => null,
    'motto' => null,
    'visionBody' => null,
    'missions' => null,
])

@php
    $c = $content ?? [];
    $rawMotto = $c['motto'] ?? $motto ?? 'Integrity, Unbreakable, We Are Competent!!!';
    $displayMotto = ($rawMotto === 'Integrity, Unbreakable, We Are Competent!!!')
        ? 'SOLID LIKE A BEE, BE LIKE WE WANT TO BE, STRONG AND FREE!'
        : $rawMotto;
    $visionBody = $c['vision_body'] ?? $visionBody ?? 'Menjadi angkatan Teknik Komputer 2026 (COMETS) yang solid layaknya satu koloni, berintegritas tinggi, dan tangguh dalam kompetensi rekayasa perangkat keras maupun perangkat lunak, serta mampu memberikan kontribusi nyata bagi kemajuan teknologi dan masyarakat.';
    $missions = $c['missions'] ?? $missions ?? [
        [
            'number' => '01.',
            'title' => 'Solidaritas & Inklusivitas Tanpa Sekat',
            'description' => 'Membangun iklim kekeluargaan koloni yang suportif lintas kelas A, B, C, dan D sehingga setiap simpul anggota tumbuh bersama tanpa ada yang tertinggal.',
        ],
        [
            'number' => '02.',
            'title' => 'Akselerasi Kompetensi Akademik & Rekayasa',
            'description' => 'Mendorong budaya kolaborasi riset, praktikum sistem komputer, dan pengembangan karya inovatif yang berdaya saing nasional maupun global.',
        ],
        [
            'number' => '03.',
            'title' => 'Integritas Karakter & Kontribusi Berkelanjutan',
            'description' => 'Menjaga ketangguhan mental, etika profesional, serta meninggalkan jejak kontribusi positif di lingkungan Departemen Teknik Komputer FT Undip.',
        ],
    ];
@endphp

{{-- ==========================================================================
     IDENTITY — SECTION 2: VISI & MISI ASYMMETRIC 2-COLUMN BENTO GRID
     Mengacu pada .revamp-ui/PRD.md §3.2.2, DESIGN_TASTE.md & DESIGN_TOKENS.md
     ========================================================================== --}}
<section
    id="identity-vision-mission-section"
    aria-labelledby="vision-mission-heading"
    {{ $attributes->merge(['class' => 'relative w-full bg-ambient-colony text-text-primary py-12 sm:py-16 border-t border-white/10 overflow-hidden']) }}
>
    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="mb-8 sm:mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-white/10 pb-5">
            <div>
                <p class="font-mono text-xs font-bold uppercase tracking-wider text-brand-gold mb-2">
                    &gt; // 01_VISION_MISSION // COLONY_DIRECTIVES
                </p>
                <h2
                    id="vision-mission-heading"
                    class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-text-primary tracking-tight leading-tight"
                >
                    Arah Gerak &amp; Komitmen Angkatan
                </h2>
            </div>
            <span class="px-3 py-1 rounded-md bg-brand-red/30 border border-brand-red-hover/60 font-mono text-xs uppercase tracking-wider text-text-primary">
                STRATEGIC_PILLAR // 2026–2030
            </span>
        </div>

        {{-- 2-Column Bento Grid: Left Visi Card (Tech Gold Pillar) & Right Misi Card (Blood Red Pillar) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

            {{-- ── LEFT CARD: VISI ANGKATAN (lg:col-span-5) ── --}}
            <article class="lg:col-span-5 rounded-xl border border-brand-gold/30 border-t-4 border-t-brand-gold bg-surface-card/90 p-6 sm:p-8 flex flex-col justify-between gap-6 glow-gold-subtle">
                <div>
                    {{-- Top Status Pill Badge --}}
                    <div class="flex items-center justify-between gap-2 mb-6">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-brand-gold/15 border border-brand-gold/40 text-brand-gold font-mono text-xs font-bold uppercase tracking-wider">
                            <span class="size-1.5 rounded-full bg-brand-gold-glow animate-pulse" aria-hidden="true"></span>
                            <span>[ VISI_UTAMA ]</span>
                        </span>
                        <span class="font-mono text-[10px] uppercase tracking-wider text-text-muted">
                            CORE_DIRECTION
                        </span>
                    </div>

                    {{-- Official Jargon / Motto Callout with Blood Red Left Border --}}
                    <blockquote class="border-l-4 border-l-brand-red bg-brand-red/15 border border-brand-red-hover/40 rounded-r-xl pl-4 pr-3 py-3 my-4 glow-red-subtle">
                        <p class="font-display text-lg sm:text-xl font-bold text-text-primary tracking-tight leading-snug">
                            "{{ $displayMotto }}"
                        </p>
                    </blockquote>

                    {{-- Screen-Reader Metadata for Dynamic Hydration & Test Compatibility --}}
                    <div class="sr-only">
                        <span>{{ $rawMotto }}</span>
                    </div>

                    {{-- Vision Narrative Paragraph --}}
                    <p class="mt-5 font-body text-sm sm:text-base leading-relaxed text-text-secondary">
                        {{ $visionBody }}
                    </p>
                </div>

                {{-- Bottom Strategic Pillar Pill --}}
                <div class="pt-4 border-t border-white/10 flex items-center justify-between gap-2">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-md bg-canvas-base/80 border border-white/10 font-mono text-[11px] font-semibold uppercase tracking-wider text-brand-gold">
                        COMETS // 2026–2030
                    </span>
                    <span class="font-mono text-xs text-brand-gold font-bold" aria-hidden="true">
                        [HEX_01]
                    </span>
                </div>
            </article>

            {{-- ── RIGHT CARD: MISI ANGKATAN (lg:col-span-7) ── --}}
            <article class="lg:col-span-7 rounded-xl border border-brand-red-hover/60 border-t-4 border-t-brand-red bg-surface-card/90 p-6 sm:p-8 flex flex-col justify-between glow-red-subtle">
                <div>
                    {{-- Top Header Row --}}
                    <div class="flex items-center justify-between gap-2 pb-5 mb-6 border-b border-white/10">
                        <span class="inline-flex items-center px-3 py-1 rounded-md bg-brand-red border border-brand-red-hover font-mono text-xs font-bold uppercase tracking-wider text-text-primary">
                            [ MISI_EKSEKUSI ]
                        </span>
                        <span class="font-mono text-[11px] uppercase tracking-wider text-brand-gold">
                            03_ACTION_DIRECTIVES
                        </span>
                    </div>

                    {{-- 3-Item Numbered Vertical List Separated by Hairline Dividers --}}
                    <ol class="divide-y divide-white/10">
                        @foreach ($missions as $mission)
                            <li class="{{ $loop->first ? 'pb-5' : ($loop->last ? 'pt-5' : 'py-5') }} flex items-start gap-4">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md font-mono text-sm sm:text-base font-bold shrink-0 {{ $loop->iteration === 2 ? 'bg-brand-red border border-brand-red-hover text-text-primary' : 'bg-brand-gold/15 border border-brand-gold/40 text-brand-gold' }}">
                                    {{ $mission['number'] }}
                                </span>
                                <div class="space-y-1.5">
                                    <h3 class="font-display text-base sm:text-lg font-bold text-text-primary leading-snug">
                                        {{ $mission['title'] }}
                                    </h3>
                                    <p class="font-body text-sm text-text-secondary leading-relaxed">
                                        {{ $mission['description'] }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>

                {{-- Bottom Verification Footer --}}
                <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between gap-2 font-mono text-[10px] uppercase tracking-wider text-text-muted">
                    <span>EXECUTION_FRAMEWORK // TEKKOM_UNDIP</span>
                    <span class="text-brand-gold font-bold">STATUS: ACTIVE_COLONY</span>
                </div>
            </article>

        </div>

    </div>
</section>
