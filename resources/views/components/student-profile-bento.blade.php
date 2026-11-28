@props([
    'student',
    'prevStudent' => null,
    'nextStudent' => null,
    'currentPosition' => 1,
    'totalInClass' => 1,
])

<div class="w-full">

    {{-- Top Navigation & Prev/Next Strip --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6 pt-2 pb-4 border-b border-white/10">
        <div class="flex items-center gap-3">
            <a
                href="{{ route('directory.index') }}"
                class="inline-flex items-center gap-2 min-h-10 px-4 py-2 bg-surface-card border border-white/15 rounded-xl font-display text-xs sm:text-sm font-semibold text-text-primary hover:border-brand-gold hover:text-brand-gold transition-colors group"
                aria-label="Kembali ke Direktori Mahasiswa"
            >
                <span class="font-mono text-brand-gold group-hover:-translate-x-0.5 transition-transform" aria-hidden="true">&larr;</span>
                <span>Kembali ke Direktori</span>
            </a>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($prevStudent)
                <a
                    href="/students/{{ $prevStudent['nim'] }}"
                    class="inline-flex items-center gap-1.5 min-h-9 px-3 py-1.5 rounded-xl bg-surface-card border border-white/15 hover:border-brand-gold font-display text-xs font-medium text-text-secondary hover:text-brand-gold transition-colors whitespace-nowrap"
                    title="Mahasiswa sebelumnya: {{ $prevStudent['nickname'] }}"
                >
                    <span aria-hidden="true">&larr;</span>
                    <span>{{ $prevStudent['nickname'] }}</span>
                </a>
            @endif

            <span class="px-3 py-1.5 rounded-xl bg-canvas-base border border-white/10 font-mono text-xs font-semibold text-brand-gold whitespace-nowrap">
                {{ $currentPosition }} / {{ $totalInClass }}
            </span>

            @if ($nextStudent)
                <a
                    href="/students/{{ $nextStudent['nim'] }}"
                    class="inline-flex items-center gap-1.5 min-h-9 px-3 py-1.5 rounded-xl bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover font-display text-xs font-semibold text-text-primary transition-colors whitespace-nowrap"
                    title="Mahasiswa berikutnya: {{ $nextStudent['nickname'] }}"
                >
                    <span>{{ $nextStudent['nickname'] }}</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Split Bento Profile Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- LEFT COLUMN: Portrait Card + MBTI Profile & 4 Spectrum Bars --}}
        <div class="lg:col-span-5 flex flex-col gap-6">
        <div
            id="candidate-card-profile"
            x-data="{
                photoMode: 'formal',
                copied: false,
                setMode(mode) {
                    this.photoMode = mode;
                    if (window.syncPhotoMode) window.syncPhotoMode(mode);
                },
                copyNim() {
                    navigator.clipboard.writeText('{{ $student['nim'] }}');
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                }
            }"
            class="bg-surface-card/85 border border-white/10 rounded-2xl p-5 sm:p-6 flex flex-col gap-5"
        >
            {{-- View Mode Switcher (Formal / Kasual, >= 36px height) --}}
            <div class="flex items-center justify-between gap-2">
                <span class="font-display text-xs font-semibold text-text-secondary">
                    Mode Foto
                </span>
                <div class="inline-flex items-center p-1 rounded-xl bg-canvas-base border border-white/10 gap-1">
                    <button
                        type="button"
                        id="btn-photo-formal"
                        @click="setMode('formal')"
                        onclick="window.setStudentPhotoMode && window.setStudentPhotoMode('formal')"
                        :class="photoMode === 'formal' ? 'bg-brand-red text-text-primary font-semibold border border-brand-red-hover' : 'text-text-secondary hover:text-brand-gold border border-transparent'"
                        class="photo-toggle-btn min-h-9 px-3.5 py-1.5 rounded-lg text-xs font-display transition-colors cursor-pointer bg-brand-red text-text-primary font-semibold border border-brand-red-hover whitespace-nowrap"
                        data-mode="formal"
                    >
                        <span>Formal</span>
                    </button>

                    <button
                        type="button"
                        id="btn-photo-casual"
                        @click="setMode('casual')"
                        onclick="window.setStudentPhotoMode && window.setStudentPhotoMode('casual')"
                        :class="photoMode === 'casual' ? 'bg-brand-red text-text-primary font-semibold border border-brand-red-hover' : 'text-text-secondary hover:text-brand-gold border border-transparent'"
                        class="photo-toggle-btn min-h-9 px-3.5 py-1.5 rounded-lg text-xs font-display transition-colors cursor-pointer text-text-secondary hover:text-brand-gold border border-transparent whitespace-nowrap"
                        data-mode="casual"
                    >
                        <span>Kasual</span>
                    </button>
                </div>
            </div>

            {{-- Portrait Frame 3:4 --}}
            <div class="relative w-full aspect-[3/4] rounded-xl overflow-hidden border border-white/10 bg-canvas-base">
                {{-- Formal Layer --}}
                <div
                    id="container-photo-formal"
                    class="photo-view-layer absolute inset-0 w-full h-full transition-opacity duration-300 opacity-100 z-10"
                    :class="photoMode === 'formal' ? 'opacity-100 pointer-events-auto z-10' : 'opacity-0 pointer-events-none z-0'"
                >
                    @if (!empty($student['formal_photo_url']))
                        <img
                            id="img-photo-formal"
                            src="{{ $student['formal_photo_url'] }}"
                            alt="Foto resmi {{ $student['name'] }}"
                            class="w-full h-full object-cover"
                            fetchpriority="high"
                            onerror="this.style.display='none'; document.getElementById('placeholder-formal').style.display='flex';"
                        />
                    @endif

                    <div
                        id="placeholder-formal"
                        class="w-full h-full"
                        style="{{ !empty($student['formal_photo_url']) ? 'display: none;' : 'display: flex;' }}"
                    >
                        <x-wireframe-slot variant="emblem" label="Foto Formal" hint="Jas Almamater Undip" />
                    </div>

                    <div class="absolute bottom-3 right-3 z-20 px-2.5 py-1 rounded-lg text-xs font-display font-medium bg-canvas-base/85 border border-white/15 text-brand-gold backdrop-blur-xs pointer-events-none">
                        Jas Almamater
                    </div>
                </div>

                {{-- Casual Layer --}}
                <div
                    id="container-photo-casual"
                    class="photo-view-layer absolute inset-0 w-full h-full transition-opacity duration-300 opacity-0 pointer-events-none z-0"
                    :class="photoMode === 'casual' ? 'opacity-100 pointer-events-auto z-10' : 'opacity-0 pointer-events-none z-0'"
                >
                    @if (!empty($student['casual_photo_url']))
                        <img
                            id="img-photo-casual"
                            src="{{ $student['casual_photo_url'] }}"
                            alt="Foto kasual {{ $student['nickname'] }}"
                            class="w-full h-full object-cover"
                            loading="lazy"
                            onerror="this.style.display='none'; document.getElementById('placeholder-casual').style.display='flex';"
                        />
                    @endif

                    <div
                        id="placeholder-casual"
                        class="w-full h-full"
                        style="{{ !empty($student['casual_photo_url']) ? 'display: none;' : 'display: flex;' }}"
                    >
                        <x-wireframe-slot variant="emblem" label="Foto Kasual" hint="Gaya Bebas" />
                    </div>

                    <div class="absolute bottom-3 right-3 z-20 px-2.5 py-1 rounded-lg text-xs font-display font-medium bg-brand-red/90 border border-brand-red-hover text-text-primary backdrop-blur-xs pointer-events-none">
                        Foto Kasual
                    </div>
                </div>
            </div>

            {{-- Quick Tags & Copy NIM --}}
            <div class="space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-lg bg-brand-gold/15 border border-brand-gold/35 text-brand-gold font-display text-xs font-semibold">
                        Kelas {{ $student['class_name'] }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-canvas-base border border-white/10 font-body text-xs text-text-secondary">
                        {{ $student['origin_city'] }}
                    </span>
                </div>

                <div class="flex items-center justify-between gap-2 p-2.5 pl-3.5 rounded-xl bg-canvas-base border border-white/10 font-mono text-xs text-text-primary font-semibold">
                    <span class="text-brand-gold">NIM: {{ $student['nim'] }}</span>
                    <button
                        type="button"
                        @click="copyNim()"
                        class="inline-flex items-center justify-center gap-1.5 min-h-9 px-3 py-1.5 rounded-lg bg-surface-elevated hover:border-brand-gold/40 border border-white/10 text-text-secondary hover:text-brand-gold transition-colors cursor-pointer whitespace-nowrap"
                        :title="copied ? 'Tersalin!' : 'Salin NIM'"
                        aria-label="Salin NIM ke clipboard"
                    >
                        <template x-if="!copied">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="size-3.5 stroke-[2]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                </svg>
                                <span class="font-display text-xs">Salin</span>
                            </span>
                        </template>
                        <template x-if="copied">
                            <span class="font-display text-xs text-brand-gold font-semibold">Tersalin</span>
                        </template>
                    </button>
                </div>
            </div>

            {{-- Instagram Link --}}
            <div>
                <a
                    href="{{ $student['instagram_url'] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center justify-center gap-2.5 w-full min-h-11 py-3 px-4 rounded-xl bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary font-display text-sm font-semibold transition-colors"
                >
                    <svg class="size-4 shrink-0 text-brand-gold" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                    </svg>
                    <span>Instagram {{ '@' . $student['instagram_username'] }}</span>
                    <span class="font-mono text-xs text-brand-gold" aria-hidden="true">&#8599;</span>
                </a>
            </div>
        </div>

        {{-- MBTI Profile & 4 Spectrum Bars (placed in empty space below Portrait Card) --}}
        @if (! empty($student['mbti']))
            @php
                $mbti = $student['mbti'];
                $pcts = $mbti['pcts'] ?? [];
                $axes = [
                    ['left' => 'E', 'leftLabel' => 'Extravert', 'right' => 'I', 'rightLabel' => 'Introvert', 'name' => 'Energi'],
                    ['left' => 'N', 'leftLabel' => 'Intuition', 'right' => 'S', 'rightLabel' => 'Sensing', 'name' => 'Persepsi'],
                    ['left' => 'T', 'leftLabel' => 'Thinking', 'right' => 'F', 'rightLabel' => 'Feeling', 'name' => 'Keputusan'],
                    ['left' => 'J', 'leftLabel' => 'Judging', 'right' => 'P', 'rightLabel' => 'Perceiving', 'name' => 'Gaya Kerja'],
                ];
            @endphp
            <section
                id="student-mbti-card"
                aria-label="Profil Kepribadian MBTI"
                class="bg-surface-card/85 border border-white/10 rounded-2xl p-5 sm:p-6 space-y-5"
            >
                <div class="flex items-start justify-between gap-3 pb-4 border-b border-white/10">
                    <div>
                        <p class="font-display text-xs font-semibold uppercase tracking-wider text-brand-gold mb-1">
                            Profil Kepribadian &middot; MBTI
                        </p>
                        <h2 class="font-display text-xl font-bold text-text-primary tracking-tight">
                            {{ $mbti['title'] ?? $mbti['code'] }}
                        </h2>
                        @if (! empty($mbti['temperament_name']))
                            <span class="mt-1.5 inline-flex items-center px-2.5 py-0.5 rounded-lg bg-canvas-base border border-white/10 font-mono text-[11px] text-text-secondary">
                                {{ $mbti['temperament_name'] }} &middot; {{ $mbti['temperament_code'] }}
                            </span>
                        @endif
                    </div>

                    <span class="shrink-0 inline-flex items-center justify-center px-3.5 py-2 rounded-xl bg-brand-red border border-brand-gold/60 font-mono text-lg font-bold text-text-primary tracking-widest">
                        {{ $mbti['code'] }}
                    </span>
                </div>

                @if (! empty($mbti['description']))
                    <p class="font-body text-xs sm:text-sm text-text-secondary leading-relaxed">
                        {{ $mbti['description'] }}
                    </p>
                @endif

                <div class="space-y-3.5">
                    @foreach ($axes as $axis)
                        @php
                            $leftVal = (int) ($pcts[$axis['left']] ?? 50);
                            $rightVal = (int) ($pcts[$axis['right']] ?? 50);
                            $leftDominant = $leftVal >= $rightVal;
                            $dominantWidth = max($leftVal, $rightVal);
                        @endphp
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between gap-2 text-xs">
                                <span class="font-display {{ $leftDominant ? 'font-bold text-brand-gold' : 'font-medium text-text-muted' }}">
                                    {{ $axis['leftLabel'] }} ({{ $axis['left'] }}) <span class="font-mono">{{ $leftVal }}%</span>
                                </span>
                                <span class="font-display {{ ! $leftDominant ? 'font-bold text-brand-gold' : 'font-medium text-text-muted' }}">
                                    <span class="font-mono">{{ $rightVal }}%</span> ({{ $axis['right'] }}) {{ $axis['rightLabel'] }}
                                </span>
                            </div>
                            <div class="relative h-2.5 w-full rounded-full bg-canvas-base border border-white/10 overflow-hidden">
                                @if ($leftDominant)
                                    <div
                                        class="absolute inset-y-0 left-0 rounded-full bg-brand-gold transition-all duration-300"
                                        style="width: {{ $dominantWidth }}%;"
                                    ></div>
                                @else
                                    <div
                                        class="absolute inset-y-0 right-0 rounded-full bg-brand-red-hover transition-all duration-300"
                                        style="width: {{ $dominantWidth }}%;"
                                    ></div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
        </div>

        {{-- RIGHT COLUMN: Identity, Quote, Bio Grid, Lifestyle, Spotify --}}
        <div class="lg:col-span-7 flex flex-col gap-6">

            {{-- Header Identity & Quote --}}
            <div class="bg-surface-card/85 border border-white/10 rounded-2xl p-6 sm:p-8 space-y-6">
                <div>
                    <p class="font-display text-xs font-semibold uppercase tracking-wider text-brand-gold mb-2">
                        Nama Panggilan: {{ $student['nickname'] }}
                    </p>
                    <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-text-primary tracking-tight leading-tight">
                        {{ $student['name'] }}
                    </h1>
                </div>

                <div class="rounded-xl border border-brand-red-hover/40 bg-brand-red/12 p-4 sm:p-5">
                    <blockquote class="font-display italic text-base sm:text-lg font-medium leading-relaxed text-text-primary">
                        “{{ $student['bio_quote'] }}”
                    </blockquote>
                </div>
            </div>

            {{-- Biodata Grid --}}
            <div class="bg-surface-card/85 border border-white/10 rounded-2xl p-6 sm:p-8">
                <h2 class="font-display text-xl sm:text-2xl font-bold text-text-primary tracking-tight mb-5 pb-4 border-b border-white/10">
                    Informasi Identitas &amp; Kependudukan
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-canvas-base/80 border border-white/10 rounded-xl p-4">
                        <span class="font-display text-xs font-medium text-text-muted mb-1.5 block">
                            Asal Kota / Daerah
                        </span>
                        <p class="font-display text-base font-bold text-text-primary">
                            {{ $student['origin_city'] }}
                        </p>
                    </div>

                    <div class="bg-canvas-base/80 border border-white/10 rounded-xl p-4">
                        <span class="font-display text-xs font-medium text-text-muted mb-1.5 block">
                            Tempat, Tanggal Lahir
                        </span>
                        <p class="font-display text-base font-bold text-text-primary leading-snug">
                            {{ $student['birth_place'] }}, {{ $student['birth_date'] }}
                        </p>
                    </div>

                    <div class="bg-canvas-base/80 border border-white/10 rounded-xl p-4">
                        <span class="font-display text-xs font-medium text-text-muted mb-1.5 block">
                            Usia Saat Ini
                        </span>
                        <p class="font-display text-base font-bold text-text-primary">
                            {{ $student['age'] ? $student['age'].' Tahun' : 'Tidak tersedia' }}
                        </p>
                    </div>

                    <div class="bg-canvas-base/80 border border-white/10 rounded-xl p-4">
                        <span class="font-display text-xs font-medium text-text-muted mb-1.5 block">
                            Agama / Kepercayaan
                        </span>
                        <p class="font-display text-base font-bold text-text-primary">
                            {{ $student['religion'] }}
                        </p>
                    </div>

                    <div class="md:col-span-2 bg-canvas-base/80 border border-white/10 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="font-display text-xs font-medium text-text-muted mb-1 block">
                                Program Studi &amp; Fakultas
                            </span>
                            <p class="font-display text-base font-bold text-text-primary">
                                S1 Teknik Komputer, Fakultas Teknik
                            </p>
                        </div>
                        <span class="font-display text-xs font-medium text-brand-gold self-start sm:self-auto whitespace-nowrap">
                            Kampus Tembalang, Semarang
                        </span>
                    </div>
                </div>
            </div>

            {{-- Hobbies & Favorite Food --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">
                <div class="md:col-span-6 bg-surface-card/85 border border-white/10 rounded-2xl p-6">
                    <h3 class="font-display text-base sm:text-lg font-bold text-text-primary mb-3.5">
                        Hobi &amp; Minat
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($student['hobbies'] as $hobby)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg font-body text-xs font-medium bg-canvas-base text-brand-gold border border-brand-gold/30">
                                #{{ ucfirst($hobby) }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="md:col-span-6 bg-surface-card/85 border border-white/10 rounded-2xl p-6">
                    <h3 class="font-display text-base sm:text-lg font-bold text-text-primary mb-3.5">
                        Tempat Makan Favorit
                    </h3>
                    <div class="bg-canvas-base/85 border border-white/10 rounded-xl p-3.5">
                        <p class="font-display font-bold text-sm sm:text-base text-text-primary">
                            {{ $student['favorite_food_place'] }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Spotify Embed --}}
            <div class="w-full bg-surface-card/85 text-text-primary border border-white/10 rounded-2xl p-6 sm:p-7">
                <h3 class="font-display text-base sm:text-lg font-bold text-text-primary mb-4">
                    {{ ($student['spotify_type'] ?? 'track') === 'playlist' ? 'Playlist Favorit di Spotify' : 'Lagu Favorit di Spotify' }}
                </h3>

                @if ($student['spotify_track_id'])
                    <div x-data="{ loaded: false }" class="relative min-h-[352px] overflow-hidden rounded-xl bg-canvas-base border border-white/10">
                        <div
                            x-show="!loaded"
                            id="spotify-loading"
                            class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-3 bg-canvas-base p-5 text-center"
                            aria-hidden="true"
                        >
                            <div class="w-40 h-3 animate-pulse rounded bg-brand-gold/20"></div>
                            <p class="font-body text-xs text-text-muted">Memuat pemutar Spotify...</p>
                        </div>
                        <p id="spotify-status" class="sr-only" role="status" aria-live="polite">Pemutar Spotify siap.</p>
                        <iframe
                            id="spotify-embed"
                            data-testid="embed-iframe"
                            title="{{ ($student['spotify_type'] ?? 'track') === 'playlist' ? 'Playlist' : 'Lagu' }} favorit {{ $student['nickname'] }} di Spotify"
                            style="border-radius:12px"
                            src="https://open.spotify.com/embed/{{ $student['spotify_type'] ?? 'track' }}/{{ $student['spotify_track_id'] }}?utm_source=generator"
                            width="100%"
                            height="352"
                            frameborder="0"
                            allowfullscreen=""
                            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                            loading="lazy"
                            @load="loaded = true"
                        ></iframe>
                    </div>
                @else
                    <div class="rounded-xl bg-canvas-base/80 border border-white/10 p-6 text-center text-sm text-text-secondary">
                        <p role="status">Spotify belum ditambahkan.</p>
                    </div>
                @endif
            </div>

        </div>

    </div>

    {{-- Bottom Previous / Next Navigation --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mt-10 pt-6 border-t border-white/10">
        @if ($prevStudent)
            <a
                href="/students/{{ $prevStudent['nim'] }}"
                class="group inline-flex items-center gap-2 min-h-11 px-4 sm:px-5 py-2.5 rounded-xl font-display text-xs sm:text-sm font-semibold text-text-primary bg-surface-card border border-white/15 hover:border-brand-gold hover:text-brand-gold transition-colors whitespace-nowrap"
                aria-label="Mahasiswa sebelumnya: {{ $prevStudent['nickname'] }}"
            >
                <span class="font-mono text-brand-gold group-hover:-translate-x-0.5 transition-transform" aria-hidden="true">&larr;</span>
                <span class="hidden sm:inline text-text-secondary">Sebelumnya:</span>
                <span class="font-bold">{{ $prevStudent['nickname'] }}</span>
            </a>
        @else
            <div></div>
        @endif

        <span class="font-mono text-xs text-brand-gold font-semibold">
            {{ $currentPosition }} / {{ $totalInClass }} (Kelas {{ $student['class_name'] }})
        </span>

        @if ($nextStudent)
            <a
                href="/students/{{ $nextStudent['nim'] }}"
                class="group inline-flex items-center gap-2 min-h-11 px-4 sm:px-5 py-2.5 rounded-xl font-display text-xs sm:text-sm font-semibold text-text-primary bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover transition-colors whitespace-nowrap"
                aria-label="Mahasiswa berikutnya: {{ $nextStudent['nickname'] }}"
            >
                <span class="hidden sm:inline text-text-secondary">Selanjutnya:</span>
                <span class="font-bold">{{ $nextStudent['nickname'] }}</span>
                <span class="font-mono text-brand-gold group-hover:translate-x-0.5 transition-transform" aria-hidden="true">&rarr;</span>
            </a>
        @else
            <div></div>
        @endif
    </div>

</div>

<script>
    (() => {
        window.currentStudentPhotoMode = 'formal';

        window.setStudentPhotoMode = function (mode) {
            window.currentStudentPhotoMode = mode;

            const btnFormal = document.getElementById('btn-photo-formal');
            const btnCasual = document.getElementById('btn-photo-casual');
            const containerFormal = document.getElementById('container-photo-formal');
            const containerCasual = document.getElementById('container-photo-casual');

            const activeClasses = ['bg-brand-red', 'text-text-primary', 'font-semibold', 'border-brand-red-hover'];
            const inactiveClasses = ['text-text-secondary', 'hover:text-brand-gold', 'border-transparent'];

            if (mode === 'formal') {
                if (btnFormal) {
                    btnFormal.classList.add(...activeClasses);
                    btnFormal.classList.remove(...inactiveClasses);
                }
                if (btnCasual) {
                    btnCasual.classList.remove(...activeClasses);
                    btnCasual.classList.add(...inactiveClasses);
                }

                if (containerFormal) {
                    containerFormal.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
                    containerFormal.classList.add('opacity-100', 'pointer-events-auto', 'z-10');
                }
                if (containerCasual) {
                    containerCasual.classList.remove('opacity-100', 'pointer-events-auto', 'z-10');
                    containerCasual.classList.add('opacity-0', 'pointer-events-none', 'z-0');
                }
            } else {
                if (btnCasual) {
                    btnCasual.classList.add(...activeClasses);
                    btnCasual.classList.remove(...inactiveClasses);
                }
                if (btnFormal) {
                    btnFormal.classList.remove(...activeClasses);
                    btnFormal.classList.add(...inactiveClasses);
                }

                if (containerCasual) {
                    containerCasual.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
                    containerCasual.classList.add('opacity-100', 'pointer-events-auto', 'z-10');
                }
                if (containerFormal) {
                    containerFormal.classList.remove('opacity-100', 'pointer-events-auto', 'z-10');
                    containerFormal.classList.add('opacity-0', 'pointer-events-none', 'z-0');
                }
            }
        };

        window.syncPhotoMode = function (mode) {
            window.setStudentPhotoMode(mode);
        };

        document.addEventListener('DOMContentLoaded', () => {
            window.setStudentPhotoMode('formal');
        });
    })();
</script>
