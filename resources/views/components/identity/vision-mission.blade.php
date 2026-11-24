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
            'number' => '01',
            'title' => 'Solidaritas & Inklusivitas Tanpa Sekat',
            'description' => 'Membangun iklim kekeluargaan koloni yang suportif lintas kelas A, B, C, dan D sehingga setiap anggota tumbuh bersama tanpa ada yang tertinggal.',
        ],
        [
            'number' => '02',
            'title' => 'Akselerasi Kompetensi Akademik & Rekayasa',
            'description' => 'Mendorong budaya kolaborasi riset, praktikum sistem komputer, dan pengembangan karya inovatif yang berdaya saing nasional maupun global.',
        ],
        [
            'number' => '03',
            'title' => 'Integritas Karakter & Kontribusi Berkelanjutan',
            'description' => 'Menjaga ketangguhan mental, etika profesional, serta meninggalkan jejak kontribusi positif di lingkungan Departemen Teknik Komputer FT Undip.',
        ],
    ];
@endphp

<section
    id="identity-vision-mission-section"
    aria-labelledby="vision-mission-heading"
    {{ $attributes->merge(['class' => 'relative w-full bg-ambient-colony text-text-primary py-14 sm:py-20 border-t border-white/10 overflow-hidden']) }}
>
    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="mb-10 flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-white/10 pb-6">
            <div>
                <p class="font-display text-xs font-semibold uppercase tracking-widest text-brand-gold mb-2">
                    Visi &amp; Misi &middot; 2026&ndash;2030
                </p>
                <h2
                    id="vision-mission-heading"
                    class="font-display text-2xl sm:text-3xl lg:text-4xl font-bold text-text-primary tracking-tight leading-tight"
                >
                    Arah Gerak &amp; Komitmen Angkatan
                </h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">

            {{-- Left Column: Visi Utama --}}
            <article class="lg:col-span-5 rounded-2xl border border-white/10 bg-surface-card/75 p-6 sm:p-8 space-y-6">
                <div class="flex items-center justify-between gap-2">
                    <span class="font-display text-xs font-semibold uppercase tracking-wider text-brand-gold">
                        Visi Utama
                    </span>
                </div>

                <blockquote class="rounded-xl border border-brand-red-hover/40 bg-brand-red/15 p-4 sm:p-5">
                    <p class="font-display text-lg sm:text-xl font-bold text-text-primary tracking-tight leading-snug">
                        “{{ $displayMotto }}”
                    </p>
                </blockquote>

                <p class="font-body text-sm sm:text-base leading-relaxed text-text-secondary">
                    {{ $visionBody }}
                </p>
            </article>

            {{-- Right Column: Misi Eksekusi --}}
            <article class="lg:col-span-7 rounded-2xl border border-white/10 bg-surface-card/75 p-6 sm:p-8">
                <p class="font-display text-xs font-semibold uppercase tracking-wider text-brand-gold mb-6">
                    Misi Angkatan
                </p>

                <ol class="divide-y divide-white/10">
                    @foreach ($missions as $mission)
                        <li class="{{ $loop->first ? 'pb-5' : ($loop->last ? 'pt-5' : 'py-5') }} flex items-start gap-4">
                            <span class="font-mono text-sm font-bold text-brand-gold pt-0.5 shrink-0">
                                {{ str_replace('.', '', (string) $mission['number']) }}
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
            </article>

        </div>
    </div>
</section>
