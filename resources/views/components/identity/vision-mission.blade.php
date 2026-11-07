@props([
    'content' => null,
    'motto' => null,
    'visionBody' => null,
    'missions' => null,
])

@php
    $c = $content ?? [];
    $motto = $c['motto'] ?? $motto ?? 'Integrity, Unbreakable, We Are Competent!!!';
    $visionBody = $c['vision_body'] ?? $visionBody ?? 'Menjadi angkatan Teknik Komputer 2026 yang solid, berintegritas tinggi, dan unggul dalam kompetensi rekayasa perangkat keras maupun perangkat lunak, serta mampu memberikan kontribusi nyata bagi kemajuan teknologi dan masyarakat.';
    $missions = $c['missions'] ?? $missions ?? [
        [
            'number' => '01.',
            'title' => 'Solidaritas & Inklusivitas Tanpa Sekat',
            'description' => 'Membangun iklim kekeluargaan yang suportif lintas kelas A, B, C, dan D sehingga setiap anggota angkatan tumbuh bersama tanpa ada yang tertinggal.',
        ],
        [
            'number' => '02.',
            'title' => 'Akselerasi Kompetensi Akademik & Rekayasa',
            'description' => 'Mendorong budaya kolaborasi riset, praktikum sistem komputer, dan pengembangan karya inovatif yang berdaya saing nasional maupun global.',
        ],
        [
            'number' => '03.',
            'title' => 'Integritas Karakter & Kontribusi Berkelanjutan',
            'description' => 'Menjaga etika profesional, tanggung jawab sosial, serta meninggalkan jejak kontribusi positif di lingkungan Departemen Teknik Komputer FT Undip.',
        ],
    ];
@endphp

{{-- ==========================================================================
     SCREEN 5 — SECTION 2: VISI & MISI ASYMMETRIC 2-COLUMN BENTO GRID
     Mengacu pada GitHub Issue #23 & #16, DESIGN.md, dan DOCS_COLOR_CONVENTION.md
     Figma Reference: Node 1:2236 (Visi & Misi Frame)
     ========================================================================== --}}
<section
    id="identity-vision-mission-section"
    aria-labelledby="vision-mission-heading"
    {{ $attributes->merge(['class' => 'w-full bg-surface-canvas text-primary-900 py-8 sm:py-12']) }}
>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-surface-border pb-6">
            <div>
                <p class="font-mono text-xs font-bold uppercase tracking-[0.05em] text-secondary-700 mb-2">
                    // 01_VISION_MISSION
                </p>
                <h2
                    id="vision-mission-heading"
                    class="text-2xl sm:text-[26px] font-bold text-primary-900 tracking-[-0.02em] leading-tight"
                >
                    Arah Gerak & Komitmen Angkatan
                </h2>
            </div>
            <span class="font-mono text-xs uppercase tracking-[0.05em] text-primary-500">
                STRATEGIC_PILLAR // 2026–2030
            </span>
        </div>

        {{-- 2-Column Bento Grid: Left Visi Card & Right Misi Card --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

            {{-- ── LEFT CARD: VISI ANGKATAN (lg:col-span-5, Surface Snow White bg-surface-card) ── --}}
            <article class="lg:col-span-5 rounded-2xl border border-surface-border bg-surface-card p-6 sm:p-8 flex flex-col justify-between gap-6 shadow-xs">
                <div>
                    {{-- Top Status Pill Badge --}}
                    <div class="flex items-center justify-between gap-2 mb-6">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-highlight-500 text-highlight-900 font-mono text-xs font-bold uppercase tracking-[0.05em]">
                            <span class="size-1.5 rounded-full bg-highlight-900" aria-hidden="true"></span>
                            [ VISI_UTAMA ]
                        </span>
                        <span class="font-mono text-[10px] uppercase tracking-wider text-primary-500">
                            CORE_DIRECTION
                        </span>
                    </div>

                    {{-- Official Motto Quote Callout (22px Bold Italic, Sky Wash Left Border) --}}
                    <blockquote class="border-l-4 border-l-secondary-500 pl-4 py-1 my-4">
                        <p class="text-xl sm:text-[22px] font-bold italic text-primary-900 tracking-[-0.02em] leading-snug">
                            "{{ $motto }}"
                        </p>
                    </blockquote>

                    {{-- Vision Narrative Paragraph --}}
                    <p class="mt-5 text-sm sm:text-[15px] leading-[1.65] text-primary-900/85">
                        {{ $visionBody }}
                    </p>
                </div>

                {{-- Bottom Strategic Pillar Pill --}}
                <div class="pt-4 border-t border-surface-border flex items-center justify-between gap-2">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-white border border-surface-border font-mono text-[11px] font-semibold uppercase tracking-[0.05em] text-primary-900">
                        STRATEGIC_PILLAR // 2026–2030
                    </span>
                    <span class="font-mono text-xs text-secondary-700 font-bold" aria-hidden="true">
                        [+]
                    </span>
                </div>
            </article>

            {{-- ── RIGHT CARD: MISI ANGKATAN (lg:col-span-7, Surface Pure White bg-white) ── --}}
            <article class="lg:col-span-7 rounded-2xl border border-surface-border bg-white p-6 sm:p-8 flex flex-col justify-between shadow-xs">
                <div>
                    {{-- Top Header Row --}}
                    <div class="flex items-center justify-between gap-2 pb-5 mb-6 border-b border-surface-border">
                        <span class="inline-flex items-center px-3 py-1 rounded-lg bg-surface-card border border-surface-border font-mono text-xs font-bold uppercase tracking-[0.05em] text-primary-900">
                            [ MISI_EKSEKUSI ]
                        </span>
                        <span class="font-mono text-[11px] uppercase tracking-[0.05em] text-primary-500">
                            03_ACTION_DIRECTIVES
                        </span>
                    </div>

                    {{-- 3-Item Numbered Vertical List Separated by Hairline Dividers --}}
                    <ol class="divide-y divide-surface-border">
                        @foreach ($missions as $mission)
                            <li class="{{ $loop->first ? 'pb-5' : ($loop->last ? 'pt-5' : 'py-5') }} flex items-start gap-4">
                                <span class="font-mono text-lg sm:text-xl font-bold text-secondary-700 shrink-0 pt-0.5">
                                    {{ $mission['number'] }}
                                </span>
                                <div class="space-y-1.5">
                                    <h3 class="text-base font-bold text-primary-900 leading-snug">
                                        {{ $mission['title'] }}
                                    </h3>
                                    <p class="text-sm text-primary-900/80 leading-relaxed">
                                        {{ $mission['description'] }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>

                {{-- Bottom Verification Footer --}}
                <div class="mt-6 pt-4 border-t border-surface-border flex items-center justify-between gap-2 font-mono text-[10px] uppercase tracking-[0.05em] text-primary-500">
                    <span>EXECUTION_FRAMEWORK // TEKKOM_UNDIP</span>
                    <span class="text-secondary-700 font-bold">STATUS: ACTIVE</span>
                </div>
            </article>

        </div>

    </div>
</section>
