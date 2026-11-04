@props([
    'student',
    'prevStudent' => null,
    'nextStudent' => null,
    'currentPosition' => 1,
    'totalInClass' => 1,
])

{{-- ==========================================================================
     SCREEN 4: STUDENT DETAIL BENTO PROFILE
     Source of Truth: Figma Node 269:525 (Screen 4 Main Bento Grid) &
                      DESIGN.md, DOCS_COLOR_CONVENTION.md, stich-uiux/biodata-layout.png
     ========================================================================== --}}
<div class="w-full">

    {{-- ── SECTION 2: SUB-NAVIGATION & BREADCRUMB STRIP (Figma Node 1:1800) ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pt-2">
        {{-- Back Navigation Button --}}
        <div>
            <a
                href="/directory"
                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-surface-border rounded-lg text-xs sm:text-sm font-semibold text-primary-900 hover:bg-surface-card hover:border-primary-900/30 transition-all duration-180 shadow-2xs group"
                aria-label="Kembali ke Direktori Mahasiswa"
            >
                <svg class="size-4 shrink-0 stroke-[2] stroke-current text-primary-900 group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span>Kembali ke Direktori</span>
            </a>
        </div>

        {{-- Monospace Technical Breadcrumb --}}
        <div class="font-mono text-[11px] sm:text-xs font-semibold text-primary-500 tracking-wider flex items-center gap-1.5 overflow-hidden truncate">
            <span>ROOT</span>
            <span class="text-primary-900/30">/</span>
            <span>DIRECTORY</span>
            <span class="text-primary-900/30">/</span>
            <span>STUDENT</span>
            <span class="text-primary-900/30">/</span>
            <span class="text-primary-900 font-bold truncate">//{{ $student['nim'] }}</span>
        </div>
    </div>

    {{-- ── SECTION 3: EDITORIAL BENTO GRID (Figma Node 269:525) ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- ══════════════════════════════════════════════════════════════════
             KOLOM KIRI: CANDIDATE CARD (35-40% / lg:col-span-5 xl:col-span-4)
             Figma Node 269:527 (488px Desktop Width)
             ══════════════════════════════════════════════════════════════════ --}}
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
            class="lg:col-span-5 xl:col-span-4 bg-surface-card border border-surface-border rounded-2xl p-5 sm:p-6 flex flex-col gap-5 shadow-xs relative overflow-hidden transition-all duration-200"
        >
            {{-- 1. Card Top Header: // SEC_01 // CANDIDATE_CARD & [STATUS: ACTIVE_STUDENT] --}}
            <div class="flex items-center justify-between gap-2 border-b border-surface-border/60 pb-3">
                <span class="inline-flex items-center gap-1.5 font-mono text-[11px] font-bold text-secondary-900">
                    <span class="size-2 rounded-full bg-secondary-500 animate-pulse" aria-hidden="true"></span>
                    [STATUS: ACTIVE_STUDENT]
                </span>
            </div>

            {{-- 2. View Mode Switcher Bar --}}
            <div class="flex items-center justify-between gap-2 p-1.5 px-3 rounded-xl bg-white/70 border border-surface-border">
                <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-primary-500">
                    VIEW_MODE:
                </span>
                <div class="inline-flex items-center p-0.5 rounded-lg bg-black/5 border border-surface-border/50">
                    {{-- Button Formal --}}
                    <button
                        type="button"
                        id="btn-photo-formal"
                        @click="setMode('formal')"
                        onclick="window.setStudentPhotoMode && window.setStudentPhotoMode('formal')"
                        :class="photoMode === 'formal' ? 'bg-secondary-500 text-white font-semibold shadow-xs' : 'text-primary-700 hover:bg-surface-subtle'"
                        class="photo-toggle-btn px-3 py-1 rounded-md text-xs font-mono transition-all duration-200 flex items-center gap-1 cursor-pointer bg-secondary-500 text-white font-semibold shadow-xs"
                        data-mode="formal"
                    >
                        <span>Formal</span>
                    </button>

                    {{-- Button Casual --}}
                    <button
                        type="button"
                        id="btn-photo-casual"
                        @click="setMode('casual')"
                        onclick="window.setStudentPhotoMode && window.setStudentPhotoMode('casual')"
                        :class="photoMode === 'casual' ? 'bg-secondary-500 text-white font-semibold shadow-xs' : 'text-primary-700 hover:bg-surface-subtle'"
                        class="photo-toggle-btn px-3 py-1 rounded-md text-xs font-mono transition-all duration-200 flex items-center gap-1 cursor-pointer text-primary-700 hover:bg-surface-subtle"
                        data-mode="casual"
                    >
                        <span>Kasual</span>
                    </button>
                </div>
            </div>

            {{-- 3. Photo Frame Container (Ratio 1:1 — 428x428 px in Figma) --}}
            <div class="relative w-full aspect-[3/4] rounded-xl overflow-hidden border border-surface-border bg-white shadow-xs">

                {{-- ── A. FORMAL PHOTO CONTAINER ── --}}
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

                    {{-- Wireframe Crosslines Placeholder Fallback [AVATAR: FORMAL] --}}
                    <div
                        id="placeholder-formal"
                        class="w-full h-full flex flex-col items-center justify-center p-4 bg-surface-canvas relative overflow-hidden"
                        style="{{ !empty($student['formal_photo_url']) ? 'display: none;' : 'display: flex;' }}"
                    >
                        <svg class="absolute inset-0 h-full w-full stroke-primary-900/15 pointer-events-none" preserveAspectRatio="none" viewBox="0 0 100 100" fill="none">
                            <line x1="0" y1="0" x2="100" y2="100" vector-effect="non-scaling-stroke" stroke-width="1" />
                            <line x1="100" y1="0" x2="0" y2="100" vector-effect="non-scaling-stroke" stroke-width="1" />
                        </svg>
                        <div class="relative z-10 flex flex-col items-center gap-1 text-center px-4 py-2 rounded-lg bg-white/95 border border-surface-border shadow-xs">
                            <span class="font-mono text-xs font-bold tracking-wider text-primary-900">[AVATAR: FORMAL]</span>
                            <span class="font-mono text-[10px] text-primary-500 uppercase tracking-widest">[X] [PHOTO_FORMAL: 1:1]</span>
                        </div>
                    </div>

                </div>

                {{-- ── B. CASUAL PHOTO CONTAINER ── --}}
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

                    {{-- Wireframe Crosslines Placeholder Fallback [AVATAR: CASUAL] --}}
                    <div
                        id="placeholder-casual"
                        class="w-full h-full flex flex-col items-center justify-center p-4 bg-surface-canvas relative overflow-hidden"
                        style="{{ !empty($student['casual_photo_url']) ? 'display: none;' : 'display: flex;' }}"
                    >
                        <svg class="absolute inset-0 h-full w-full stroke-primary-900/15 pointer-events-none" preserveAspectRatio="none" viewBox="0 0 100 100" fill="none">
                            <line x1="0" y1="0" x2="100" y2="100" vector-effect="non-scaling-stroke" stroke-width="1" />
                            <line x1="100" y1="0" x2="0" y2="100" vector-effect="non-scaling-stroke" stroke-width="1" />
                        </svg>
                        <div class="relative z-10 flex flex-col items-center gap-1 text-center px-4 py-2 rounded-lg bg-white/95 border border-surface-border shadow-xs">
                            <span class="font-mono text-xs font-bold tracking-wider text-secondary-900">[AVATAR: CASUAL]</span>
                            <span class="font-mono text-[10px] text-primary-500 uppercase tracking-widest">[X] [PHOTO_CASUAL: 1:1]</span>
                        </div>
                    </div>

                    {{-- Bottom-Right State Tag --}}
                    <div class="absolute bottom-3 right-3 px-2.5 py-1 rounded-md text-[10px] font-mono font-bold uppercase tracking-wider bg-secondary-700/90 text-white backdrop-blur-xs shadow-xs pointer-events-none">
                        FOTO BEBAS KASUAL
                    </div>
                </div>
            </div>

            {{-- 4. Identity: Nickname & Class Badge (Figma Node 269:554) --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between gap-2">
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-primary-900 tracking-[-0.025em] leading-tight">
                        {{ $student['nickname'] }}
                    </h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-lg bg-highlight-500 text-highlight-900 font-mono text-xs font-bold tracking-wider shadow-2xs border border-highlight-700/20 whitespace-nowrap">
                        KELAS {{ $student['class_name'] }} // TEKKOM_26
                    </span>
                </div>

                {{-- Legal Full Name --}}
                <p class="text-base sm:text-lg font-semibold text-primary-900/85 leading-snug">
                    {{ $student['name'] }}
                </p>

                {{-- Monospace NIM Box with Copy Button (Figma Node 269:562) --}}
                <div class="inline-flex items-center gap-2 p-1.5 px-3 rounded-lg bg-white border border-surface-border font-mono text-xs text-primary-900 font-bold shadow-2xs">
                    <span>NIM: {{ $student['nim'] }}</span>
                    <button
                        type="button"
                        @click="copyNim()"
                        class="text-primary-500 hover:text-primary-900 transition-colors cursor-pointer"
                        :title="copied ? 'Tersalin!' : 'Salin NIM'"
                        aria-label="Salin NIM ke clipboard"
                    >
                        <template x-if="!copied">
                            <svg class="size-3.5 stroke-[2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                            </svg>
                        </template>
                        <template x-if="copied">
                            <svg class="size-3.5 stroke-[2] text-secondary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        </template>
                    </button>
                    <span x-show="copied" x-cloak class="text-[10px] text-secondary-700 font-mono">Tersalin!</span>
                </div>
            </div>

            {{-- 5. Instagram Action Button (Figma Node 269:570) --}}
            <div>
                <a
                    href="{{ $student['instagram_url'] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 via-rose-500 to-purple-600 text-white text-sm font-semibold hover:opacity-95 active:scale-[0.99] transition-all duration-180 shadow-xs group"
                >
                    <svg class="size-4 shrink-0 text-white" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                    </svg>
                    <span>{{ '@' . $student['instagram_username'] }}</span>
                    <span class="font-mono text-xs opacity-80">↗</span>
                </a>
            </div>

            {{-- 6. Aspirasi & Prinsip (Figma Node 269:579) --}}
            <div class="bg-white border-l-4 border-l-primary-900 border border-surface-border p-4 sm:p-5 rounded-r-xl shadow-xs">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="font-mono text-[10px] font-bold uppercase tracking-wider text-primary-500">
                        // ASPIRASI & PRINSIP
                    </span>
                    <span class="font-mono text-[10px] uppercase tracking-wider text-primary-500">
                        REV_01
                    </span>
                </div>
                <blockquote class="italic text-sm sm:text-[15px] font-medium leading-relaxed text-primary-900">
                    “{{ $student['bio_quote'] }}”
                </blockquote>
            </div>

            {{-- 7. Candidate Card Footer: RECORD_HASH & CAMPUS_ID (Figma Node 269:587) --}}
            <div class="flex items-center justify-between gap-2 border-t border-surface-border/60 pt-3 text-[10px] font-mono text-primary-500">
                <span>RECORD_HASH: 0x{{ substr(md5($student['nim']), 0, 6) }}</span>
                <span>CAMPUS_ID: UNDIP-FT-TK</span>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════
             KOLOM KANAN: DATA DIRI & LIFESTYLE MATRIX (65% / lg:col-span-7 xl:col-span-8)
             Figma Node 269:592 (696px Desktop Width)
             ══════════════════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-7 xl:col-span-8 flex flex-col gap-6">

            {{-- ── 1. SUB-MODUL A: INFORMASI IDENTITAS & KEPENDUDUKAN (Figma Node 269:593) ── --}}
            <div class="bg-white border border-surface-border rounded-2xl p-6 sm:p-7 shadow-xs">
                {{-- Header with VERIFIKASI_FAKULTAS: OK badge --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
                    <div>
                        <span class="font-mono text-[11px] font-bold text-primary-500 uppercase tracking-wider block">
                            // 01_BIODATA_RESMI
                        </span>
                        <h2 class="text-xl sm:text-2xl font-bold text-primary-900 tracking-tight mt-0.5">
                            Informasi Identitas & Kependudukan
                        </h2>
                    </div>
                </div>

                {{-- Structured 5-Tile Metadata Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Tile 1: Asal Kota / Daerah (with #DOMISILI) --}}
                    <div class="bg-surface-canvas border border-surface-border rounded-xl p-4 flex flex-col justify-between hover:border-primary-900/25 transition-colors">
                        <span class="font-mono text-[10px] font-semibold text-primary-500 uppercase tracking-wider mb-2 block">
                            ASAL KOTA / DAERAH
                        </span>
                        <div class="flex items-baseline justify-between gap-2">
                            <p class="text-base font-bold text-primary-900">
                                {{ $student['origin_city'] }}
                            </p>
                        </div>
                    </div>

                    {{-- Tile 2: Tempat, Tanggal Lahir --}}
                    <div class="bg-surface-canvas border border-surface-border rounded-xl p-4 flex flex-col justify-between hover:border-primary-900/25 transition-colors">
                        <span class="font-mono text-[10px] font-semibold text-primary-500 uppercase tracking-wider mb-2 block">
                            TEMPAT, TANGGAL LAHIR
                        </span>
                        <p class="text-base font-bold text-primary-900 leading-snug">
                            {{ $student['birth_place'] }}, {{ $student['birth_date'] }}
                        </p>
                    </div>

                    {{-- Tile 3: Usia Saat Ini (with GENERASI 2026 badge) --}}
                    <div class="bg-surface-canvas border border-surface-border rounded-xl p-4 flex flex-col justify-between hover:border-primary-900/25 transition-colors">
                        <span class="font-mono text-[10px] font-semibold text-primary-500 uppercase tracking-wider mb-2 block">
                            USIA SAAT INI
                        </span>
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-base font-bold text-primary-900">
                                {{ $student['age'] ? $student['age'].' Tahun' : 'Tidak tersedia' }}
                            </p>
                        </div>
                    </div>

                    {{-- Tile 4: Agama / Kepercayaan --}}
                    <div class="bg-surface-canvas border border-surface-border rounded-xl p-4 flex flex-col justify-between hover:border-primary-900/25 transition-colors">
                        <span class="font-mono text-[10px] font-semibold text-primary-500 uppercase tracking-wider mb-2 block">
                            AGAMA / KEPERCAYAAN
                        </span>
                        <p class="text-base font-bold text-primary-900">
                            {{ $student['religion'] }}
                        </p>
                    </div>

                    {{-- Tile 5: Departemen / Fakultas (Full Width 2 Kolom) with KAMPUS TEMBALANG --}}
                    <div class="md:col-span-2 bg-surface-canvas border border-surface-border rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-primary-900/25 transition-colors">
                        <div>
                            <span class="font-mono text-[10px] font-semibold text-primary-500 uppercase tracking-wider mb-1 block">
                                DEPARTEMEN / FAKULTAS
                            </span>
                            <p class="text-base font-bold text-primary-900">
                                S1 Teknik Komputer, Fakultas Teknik
                            </p>
                        </div>
                        <span class="font-mono text-[10px] font-bold text-primary-700 uppercase tracking-wider bg-white border border-surface-border px-3 py-1.5 rounded-lg whitespace-nowrap self-start sm:self-auto">
                            KAMPUS TEMBALANG, SEMARANG
                        </span>
                    </div>

                </div>
            </div>

            {{-- ── 2. SUB-MODUL B: LIFESTYLE, TASTE & MUSIC (Figma Node 269:639) ── --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">

                {{-- Left Tile: Hobi & Fokus Eksplorasi + Spot Kuliner (md:col-span-6) --}}
                <div class="md:col-span-6 bg-surface-card border border-surface-border rounded-2xl p-6 flex flex-col justify-between gap-6 shadow-xs">
                    {{-- Bagian Hobi --}}
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-mono text-[11px] font-bold text-primary-500 uppercase tracking-wider">
                                // MINAT & AKTIVITAS
                            </span>
                            <span class="font-mono text-[10px] font-semibold text-primary-700">
                                [EXP: HARDWARE]
                            </span>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-primary-900 mb-3">
                            Hobi
                        </h3>

                        <div class="flex flex-wrap gap-2">
                            @foreach ($student['hobbies'] as $hobby)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-surface-border text-primary-900 shadow-2xs hover:border-secondary-500 transition-colors">
                                    #{{ ucfirst($hobby) }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    {{-- Bagian Spot Kuliner Favorit --}}
                    <div class="border-t border-surface-border/70 pt-4">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="font-mono text-[10px] font-bold text-primary-500 uppercase tracking-wider">
                                // SPOT KULINER FAVORIT
                            </span>
                        </div>

                        <div class="bg-white border border-surface-border rounded-xl p-3 sm:p-3.5 flex items-center justify-between gap-3 shadow-2xs">
                            <div class="flex items-center gap-2">
                                <span class="size-2 rounded-full bg-secondary-500 shrink-0" aria-hidden="true"></span>
                                <span class="font-bold text-sm text-primary-900">
                                    {{ $student['favorite_food_place'] }}
                                </span>
                            </div>
                            <span class="font-mono text-[10px] font-bold px-2 py-0.5 rounded bg-highlight-500/50 text-highlight-900 border border-highlight-700/20 whitespace-nowrap">
                                FAVORITE_SPOT
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Right Tile: Spotify Embed Interactive Container (md:col-span-6) --}}
                <div class="md:col-span-6 bg-primary-900 text-white border border-primary-900 rounded-2xl p-6 shadow-xs flex flex-col justify-between overflow-hidden relative">
                    {{-- Header Spotify Box --}}
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-mono text-[11px] font-bold text-secondary-300 uppercase tracking-wider">
                                // ANTHEM_TRACK // SPOTIFY_EMBED
                            </span>
                            <span class="font-mono text-[10px] bg-white/10 px-2 py-0.5 rounded text-white/70">
                                AUDIO_DEV_04
                            </span>
                        </div>

                        <h3 class="text-sm font-semibold text-white/90">
                            {{ ($student['spotify_type'] ?? 'track') === 'playlist' ? 'Playlist Favorit di Spotify' : 'Lagu Favorit di Spotify' }}
                        </h3>
                    </div>

                    {{-- Embedded Spotify Player (Logic Teknis & Skeleton Dipertahankan 100%) --}}
                    <div class="my-3">
                        @if ($student['spotify_track_id'])
                            <div class="relative min-h-[352px] overflow-hidden rounded-xl bg-black/30 border border-white/10 shadow-inner">
                                <div id="spotify-loading" class="absolute inset-0 z-10 grid content-center gap-4 bg-primary-900 p-5" aria-hidden="true">
                                    <div class="h-4 w-2/3 animate-pulse rounded bg-white/10"></div>
                                    <div class="h-3 w-1/2 animate-pulse rounded bg-white/5"></div>
                                    <div class="h-10 w-full animate-pulse rounded-lg bg-white/5"></div>
                                </div>
                                <p id="spotify-status" class="sr-only" role="status" aria-live="polite">Memuat pemutar Spotify.</p>
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
                                ></iframe>
                            </div>
                        @else
                            <div class="rounded-xl bg-white/5 border border-white/10 p-6 text-center text-sm text-white/70">
                                <span class="font-mono text-[10px] uppercase tracking-widest text-secondary-300 block mb-1">// STATUS: UNLINKED</span>
                                <p class="rounded-xl bg-brand-deep/3 p-2 text-sm text-white/70" role="status">Spotify belum ditambahkan.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Technical Spotify Footer (Figma Node 269:738) --}}
                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-white/10 text-[10px] font-mono text-white/50">
                        <span class="truncate">
                            Powered by Spotify API / Track ID: {{ $student['spotify_track_id'] ? Str::limit($student['spotify_track_id'], 14) : 'NULL' }}
                        </span>
                        <span class="text-secondary-300 font-semibold shrink-0">
                            SYNC_OK
                        </span>
                    </div>
                </div>

            </div>

            {{-- ── 3. MODUL BAWAH: ADDITIONAL ARCHITECTURAL METADATA BANNER (Figma Node 269:743) ── --}}
            <div class="bg-white border border-surface-border rounded-xl p-3 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                <div class="flex flex-wrap items-center gap-2.5">
                    <p class="text-xs text-primary-700 font-medium">
                        Data profil divalidasi oleh Biro Administrasi Akademik Mahasiswa.
                    </p>
                </div>
                <span class="font-mono text-[10px] font-bold text-secondary-900 uppercase tracking-wider self-start sm:self-auto whitespace-nowrap">
                    + CHECKSUM_VERIFIED
                </span>
            </div>

        </div>

    </div>

    {{-- ── SECTION 4: PREVIOUS / NEXT STUDENT PAGINATION BAR ── --}}
    <div class="flex items-center justify-between gap-4 mt-10 pt-6 border-t border-surface-border">
        {{-- Previous Student Link --}}
        @if ($prevStudent)
            <a
                href="/students/{{ $prevStudent['nim'] }}"
                class="group inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-lg text-xs sm:text-sm font-semibold text-primary-900 bg-white border border-surface-border hover:bg-surface-card hover:border-primary-900/30 transition-all shadow-xs"
                aria-label="Mahasiswa sebelumnya: {{ $prevStudent['nickname'] }}"
            >
                <svg class="size-4 shrink-0 stroke-[2] stroke-current group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span class="hidden sm:inline">Sebelumnya:</span>
                <span class="font-bold">{{ $prevStudent['nickname'] }}</span>
            </a>
        @else
            <div></div>
        @endif

        {{-- Class Position Counter --}}
        <span class="font-mono text-[11px] sm:text-xs text-primary-500 font-semibold text-center">
            {{ $currentPosition }} / {{ $totalInClass }} (Kelas {{ $student['class_name'] }})
        </span>

        {{-- Next Student Link --}}
        @if ($nextStudent)
            <a
                href="/students/{{ $nextStudent['nim'] }}"
                class="group inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-lg text-xs sm:text-sm font-semibold text-primary-900 bg-white border border-surface-border hover:bg-surface-card hover:border-primary-900/30 transition-all shadow-xs"
                aria-label="Mahasiswa berikutnya: {{ $nextStudent['nickname'] }}"
            >
                <span class="hidden sm:inline">Selanjutnya:</span>
                <span class="font-bold">{{ $nextStudent['nickname'] }}</span>
                <svg class="size-4 shrink-0 stroke-[2] stroke-current group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        @else
            <div></div>
        @endif
    </div>

</div>

{{-- ── 5. CLIENT-SIDE INTERACTIVITY SCRIPT (Vanilla JS & Alpine Integration) ── --}}
<script>
    (() => {
        // State Management Lokal Foto Switcher
        window.currentStudentPhotoMode = 'formal';

        window.setStudentPhotoMode = function (mode) {
            window.currentStudentPhotoMode = mode;

            const btnFormal = document.getElementById('btn-photo-formal');
            const btnCasual = document.getElementById('btn-photo-casual');
            const containerFormal = document.getElementById('container-photo-formal');
            const containerCasual = document.getElementById('container-photo-casual');

            const activeClasses = ['bg-secondary-500', 'text-white', 'font-semibold', 'shadow-xs'];
            const inactiveClasses = ['text-primary-700', 'hover:bg-surface-subtle'];

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

        // Inisialisasi saat DOM siap
        document.addEventListener('DOMContentLoaded', () => {
            window.setStudentPhotoMode('formal');
        });
    })();
</script>
