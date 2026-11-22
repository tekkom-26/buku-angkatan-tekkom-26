@props([
    'student',
    'prevStudent' => null,
    'nextStudent' => null,
    'currentPosition' => 1,
    'totalInClass' => 1,
])

@php
    $nodeNumber = str_pad((string) $currentPosition, 2, '0', STR_PAD_LEFT);
@endphp

{{-- ==========================================================================
     STUDENT DETAIL SPLIT-SCREEN BENTO PROFILE (COMETS 2026 — PRD §3.4)
     ========================================================================== --}}
<div class="w-full">

    {{-- ── SECTION 1: TOP NAVIGATION, BREADCRUMB & QUICK PREV/NEXT STRIP (PRD §3.4.1) ── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6 pt-2 pb-4 border-b border-white/10">
        {{-- Left: Back to Directory Button --}}
        <div class="flex items-center gap-3">
            <a
                href="{{ route('directory.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-surface-card border border-white/15 rounded-lg font-display text-xs sm:text-sm font-semibold text-text-primary hover:border-brand-gold hover:text-brand-gold transition-all duration-180 group"
                aria-label="Kembali ke Direktori Mahasiswa"
            >
                <span class="font-mono text-brand-gold group-hover:-translate-x-0.5 transition-transform" aria-hidden="true">&larr;</span>
                <span>Kembali ke Direktori</span>
            </a>

            {{-- Monospace Technical Breadcrumb --}}
            <div class="hidden sm:flex font-mono text-[11px] font-semibold text-text-muted tracking-wider items-center gap-1.5 overflow-hidden truncate">
                <span>ROOT</span>
                <span class="text-white/25">/</span>
                <span>DIRECTORY</span>
                <span class="text-white/25">/</span>
                <span>STUDENT</span>
                <span class="text-white/25">/</span>
                <span class="text-brand-gold font-bold truncate">// {{ $student['nim'] }}</span>
            </div>
        </div>

        {{-- Right: Quick Prev / Next Mahasiswa Navigation Buttons (PRD §3.4.1) --}}
        <div class="flex items-center gap-2 self-start md:self-auto">
            @if ($prevStudent)
                <a
                    href="/students/{{ $prevStudent['nim'] }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-card border border-white/15 hover:border-brand-gold font-mono text-xs text-text-secondary hover:text-brand-gold transition-colors"
                    title="Mahasiswa sebelumnya: {{ $prevStudent['nickname'] }}"
                >
                    <span aria-hidden="true">&larr;</span>
                    <span>[Prev: {{ $prevStudent['nickname'] }}]</span>
                </a>
            @endif

            <span class="px-2.5 py-1.5 rounded-lg bg-canvas-base border border-brand-gold/30 font-mono text-[11px] font-bold text-brand-gold">
                NODE {{ $currentPosition }}/{{ $totalInClass }}
            </span>

            @if ($nextStudent)
                <a
                    href="/students/{{ $nextStudent['nim'] }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover font-mono text-xs font-semibold text-text-primary transition-colors"
                    title="Mahasiswa berikutnya: {{ $nextStudent['nickname'] }}"
                >
                    <span>[Next: {{ $nextStudent['nickname'] }}]</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            @endif
        </div>
    </div>

    {{-- ── SECTION 2: SPLIT-SCREEN PROFILE BENTO GRID (PRD §3.4.2) ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- ══════════════════════════════════════════════════════════════════
             SISI KIRI: VISUAL HERO & CANDIDATE CARD (lg:col-span-5)
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
            class="lg:col-span-5 bg-surface-card/90 backdrop-blur-xs border border-brand-gold/35 rounded-xl p-5 sm:p-6 flex flex-col gap-5 relative overflow-hidden transition-all duration-200 glow-gold-subtle"
        >
            {{-- Top Dual-Tone Circuit Line --}}
            <div class="absolute inset-x-0 top-0 h-1 flex" aria-hidden="true">
                <div class="w-2/3 bg-brand-gold"></div>
                <div class="w-1/3 bg-brand-red"></div>
            </div>

            {{-- 1. Card Top Header: // SEC_01 // CANDIDATE_CARD & [STATUS: ACTIVE_STUDENT] --}}
            <div class="flex items-center justify-between gap-2 border-b border-white/10 pb-3 pt-1">
                <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-brand-gold">
                    // SEC_01 // CANDIDATE_CARD
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-brand-red border border-brand-red-hover font-mono text-[10px] font-bold text-text-primary">
                    <span class="size-1.5 rounded-full bg-brand-gold animate-pulse" aria-hidden="true"></span>
                    <span>[STATUS: ACTIVE_STUDENT]</span>
                </span>
            </div>

            {{-- 2. View Mode Switcher Bar (Formal vs Kasual) --}}
            <div class="flex items-center justify-between gap-2 p-1.5 px-3 rounded-lg bg-canvas-base/80 border border-white/10">
                <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-text-muted">
                    VIEW_MODE:
                </span>
                <div class="inline-flex items-center p-0.5 rounded-md bg-surface-elevated border border-white/10">
                    {{-- Button Formal --}}
                    <button
                        type="button"
                        id="btn-photo-formal"
                        @click="setMode('formal')"
                        onclick="window.setStudentPhotoMode && window.setStudentPhotoMode('formal')"
                        :class="photoMode === 'formal' ? 'bg-brand-red text-text-primary font-semibold border border-brand-red-hover' : 'text-text-secondary hover:text-brand-gold'"
                        class="photo-toggle-btn px-3 py-1 rounded text-xs font-mono transition-all duration-200 flex items-center gap-1 cursor-pointer bg-brand-red text-text-primary font-semibold border border-brand-red-hover"
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
                        :class="photoMode === 'casual' ? 'bg-brand-red text-text-primary font-semibold border border-brand-red-hover' : 'text-text-secondary hover:text-brand-gold'"
                        class="photo-toggle-btn px-3 py-1 rounded text-xs font-mono transition-all duration-200 flex items-center gap-1 cursor-pointer text-text-secondary hover:text-brand-gold"
                        data-mode="casual"
                    >
                        <span>Kasual</span>
                    </button>
                </div>
            </div>

            {{-- 3. Photo Frame Container (Ratio 3:4 Portrait in Futuristic Circuit Frame) --}}
            <div class="relative w-full aspect-[3/4] rounded-xl overflow-hidden border border-brand-gold/30 bg-canvas-base">

                {{-- Top-Left COMETS Hexagon Watermark & Top-Right Ratio Telemetry --}}
                <div class="absolute inset-x-0 top-0 z-20 flex items-center justify-between gap-2 p-3 pointer-events-none">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-canvas-base/85 backdrop-blur-xs border border-brand-gold/30 font-mono text-[10px] font-bold text-brand-gold">
                        <span>⬡ COMETS // 2026</span>
                    </span>
                    <span class="px-2 py-0.5 rounded bg-canvas-base/80 border border-white/10 font-mono text-[9px] uppercase tracking-wider text-text-muted">
                        RATIO: 1000x1000 PX
                    </span>
                </div>

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
                        class="w-full h-full flex flex-col items-center justify-center p-4 bg-canvas-base relative overflow-hidden"
                        style="{{ !empty($student['formal_photo_url']) ? 'display: none;' : 'display: flex;' }}"
                    >
                        <svg class="absolute inset-0 h-full w-full stroke-white/10 pointer-events-none" preserveAspectRatio="none" viewBox="0 0 100 100" fill="none">
                            <line x1="0" y1="0" x2="100" y2="100" vector-effect="non-scaling-stroke" stroke-width="1" />
                            <line x1="100" y1="0" x2="0" y2="100" vector-effect="non-scaling-stroke" stroke-width="1" />
                        </svg>
                        <div class="relative z-10 flex flex-col items-center gap-1 text-center px-4 py-2 rounded-lg bg-surface-card border border-brand-gold/30">
                            <span class="font-mono text-xs font-bold tracking-wider text-brand-gold">[AVATAR: FORMAL]</span>
                            <span class="font-mono text-[10px] text-text-muted uppercase tracking-widest">[X] [PHOTO_FORMAL: 3:4]</span>
                        </div>
                    </div>

                    {{-- Bottom-Right State Tag --}}
                    <div class="absolute bottom-3 right-3 z-20 px-2.5 py-1 rounded-md text-[10px] font-mono font-bold uppercase tracking-wider bg-canvas-base/90 border border-brand-gold/30 text-brand-gold backdrop-blur-xs pointer-events-none">
                        JAS ALMAMATER UNDIP
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
                        class="w-full h-full flex flex-col items-center justify-center p-4 bg-canvas-base relative overflow-hidden"
                        style="{{ !empty($student['casual_photo_url']) ? 'display: none;' : 'display: flex;' }}"
                    >
                        <svg class="absolute inset-0 h-full w-full stroke-white/10 pointer-events-none" preserveAspectRatio="none" viewBox="0 0 100 100" fill="none">
                            <line x1="0" y1="0" x2="100" y2="100" vector-effect="non-scaling-stroke" stroke-width="1" />
                            <line x1="100" y1="0" x2="0" y2="100" vector-effect="non-scaling-stroke" stroke-width="1" />
                        </svg>
                        <div class="relative z-10 flex flex-col items-center gap-1 text-center px-4 py-2 rounded-lg bg-surface-card border border-brand-red-hover">
                            <span class="font-mono text-xs font-bold tracking-wider text-brand-gold">[AVATAR: CASUAL]</span>
                            <span class="font-mono text-[10px] text-text-muted uppercase tracking-widest">[X] [PHOTO_CASUAL: 3:4]</span>
                        </div>
                    </div>

                    {{-- Bottom-Right State Tag --}}
                    <div class="absolute bottom-3 right-3 z-20 px-2.5 py-1 rounded-md text-[10px] font-mono font-bold uppercase tracking-wider bg-brand-red/90 border border-brand-red-hover text-text-primary backdrop-blur-xs pointer-events-none">
                        FOTO BEBAS KASUAL
                    </div>
                </div>
            </div>

            {{-- 4. Quick Tags: Class Badge, Origin City & NIM Copy Box --}}
            <div class="space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-md bg-brand-gold/15 border border-brand-gold/40 text-brand-gold font-mono text-xs font-bold tracking-wider">
                        KELAS {{ $student['class_name'] }} // TEKKOM_26
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-canvas-base border border-white/10 font-mono text-xs text-text-secondary">
                        <span class="text-brand-gold" aria-hidden="true">&#9679;</span>
                        <span>{{ $student['origin_city'] }}</span>
                    </span>
                </div>

                {{-- Monospace NIM Box with Copy Button --}}
                <div class="flex items-center justify-between gap-2 p-2.5 px-3.5 rounded-lg bg-canvas-base border border-white/15 font-mono text-xs text-text-primary font-bold">
                    <span class="text-brand-gold">NIM: {{ $student['nim'] }}</span>
                    <button
                        type="button"
                        @click="copyNim()"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-surface-elevated hover:border-brand-gold/40 border border-white/10 text-text-secondary hover:text-brand-gold transition-colors cursor-pointer"
                        :title="copied ? 'Tersalin!' : 'Salin NIM'"
                        aria-label="Salin NIM ke clipboard"
                    >
                        <template x-if="!copied">
                            <span class="inline-flex items-center gap-1">
                                <svg class="size-3.5 stroke-[2]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                </svg>
                                <span class="text-[10px]">COPY</span>
                            </span>
                        </template>
                        <template x-if="copied">
                            <span class="text-[10px] text-brand-gold font-bold">TERSALIN!</span>
                        </template>
                    </button>
                </div>
            </div>

            {{-- 5. Social & Contact Link: Instagram Action Button --}}
            <div>
                <a
                    href="{{ $student['instagram_url'] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center justify-center gap-2.5 w-full py-3 px-4 rounded-lg bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary font-display text-sm font-semibold transition-all duration-180 glow-red-subtle group"
                >
                    <svg class="size-4 shrink-0 text-brand-gold" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                    </svg>
                    <span>Instagram {{ '@' . $student['instagram_username'] }}</span>
                    <span class="font-mono text-xs text-brand-gold" aria-hidden="true">&#8599;</span>
                </a>
            </div>

            {{-- 6. Candidate Card Footer: RECORD_HASH & CAMPUS_ID --}}
            <div class="flex items-center justify-between gap-2 border-t border-white/10 pt-3 text-[10px] font-mono text-text-muted">
                <span>RECORD_HASH: 0x{{ substr(md5($student['nim']), 0, 6) }}</span>
                <span class="text-brand-gold">CAMPUS_ID: UNDIP-FT-TK</span>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════
             SISI KANAN: DATA & IDENTITAS PERSONAL (lg:col-span-7)
             ══════════════════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-7 flex flex-col gap-6">

            {{-- ── 1. HEADER IDENTITAS PERSONAL & PERSONAL QUOTE (PRD §3.4.2) ── --}}
            <div class="bg-surface-card/90 border border-brand-gold/30 rounded-xl p-6 sm:p-7 relative overflow-hidden glow-gold-subtle">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                    <span class="inline-flex items-center gap-2 font-mono text-xs font-bold uppercase tracking-wider text-brand-gold">
                        <span class="size-2 rounded-full bg-brand-gold-glow animate-pulse" aria-hidden="true"></span>
                        <span>&gt; STUDENT_NODE_{{ $nodeNumber }}</span>
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-md bg-canvas-base border border-white/15 font-mono text-[11px] font-bold text-brand-gold tracking-wider">
                        VERIFIKASI_FAKULTAS: OK
                    </span>
                </div>

                {{-- Full Name & Nickname Display --}}
                <div class="mb-5">
                    <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-text-primary tracking-tight leading-tight">
                        {{ $student['name'] }}
                    </h1>
                    <p class="mt-1.5 font-mono text-xs sm:text-sm uppercase tracking-wider text-brand-gold font-semibold">
                        ALIAS // "{{ $student['nickname'] }}" &middot; NIM {{ $student['nim'] }}
                    </p>
                </div>

                {{-- Personal Quote Box with Blood Red Left Border Accent (PRD §3.4.2) --}}
                <div class="border-l-4 border-l-brand-red bg-brand-red/15 border border-brand-red-hover/50 p-4 sm:p-5 rounded-r-xl glow-red-subtle">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="font-mono text-[10px] font-bold uppercase tracking-wider text-brand-gold">
                            // ASPIRASI & PRINSIP
                        </span>
                        <span class="font-mono text-[10px] uppercase tracking-wider text-text-muted">
                            REV_01
                        </span>
                    </div>
                    <blockquote class="font-display italic text-base sm:text-lg font-medium leading-relaxed text-text-primary">
                        “{{ $student['bio_quote'] }}”
                    </blockquote>
                </div>
            </div>

            {{-- ── 2. SUB-MODUL A: INFORMASI IDENTITAS & KEPENDUDUKAN ── --}}
            <div class="bg-surface-card/90 border border-white/10 rounded-xl p-6 sm:p-7">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-white/10">
                    <div>
                        <span class="font-mono text-[11px] font-bold text-brand-gold uppercase tracking-wider block">
                            // 01_BIODATA_RESMI
                        </span>
                        <h2 class="font-display text-xl sm:text-2xl font-bold text-text-primary tracking-tight mt-0.5">
                            Informasi Identitas & Kependudukan
                        </h2>
                    </div>
                    <span class="font-mono text-xs uppercase tracking-wider text-text-muted">
                        COLONY_REGISTRY // 2026
                    </span>
                </div>

                {{-- Structured 5-Tile Metadata Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Tile 1: Asal Kota / Daerah (with #DOMISILI) --}}
                    <div class="bg-canvas-base/85 border border-white/10 rounded-xl p-4 flex flex-col justify-between hover:border-brand-gold/40 transition-colors">
                        <span class="font-mono text-[10px] font-semibold text-text-muted uppercase tracking-wider mb-2 block">
                            ASAL KOTA / DAERAH
                        </span>
                        <div class="flex items-baseline justify-between gap-2">
                            <p class="font-display text-base font-bold text-text-primary">
                                {{ $student['origin_city'] }}
                            </p>
                            <span class="font-mono text-[10px] text-brand-gold font-semibold bg-brand-gold/15 border border-brand-gold/30 px-2 py-0.5 rounded">
                                #DOMISILI
                            </span>
                        </div>
                    </div>

                    {{-- Tile 2: Tempat, Tanggal Lahir --}}
                    <div class="bg-canvas-base/85 border border-white/10 rounded-xl p-4 flex flex-col justify-between hover:border-brand-gold/40 transition-colors">
                        <span class="font-mono text-[10px] font-semibold text-text-muted uppercase tracking-wider mb-2 block">
                            TEMPAT, TANGGAL LAHIR
                        </span>
                        <p class="font-display text-base font-bold text-text-primary leading-snug">
                            {{ $student['birth_place'] }}, {{ $student['birth_date'] }}
                        </p>
                    </div>

                    {{-- Tile 3: Usia Saat Ini (with GENERASI 2026 badge) --}}
                    <div class="bg-canvas-base/85 border border-white/10 rounded-xl p-4 flex flex-col justify-between hover:border-brand-gold/40 transition-colors">
                        <span class="font-mono text-[10px] font-semibold text-text-muted uppercase tracking-wider mb-2 block">
                            USIA SAAT INI
                        </span>
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-display text-base font-bold text-text-primary">
                                {{ $student['age'] ? $student['age'].' Tahun' : 'Tidak tersedia' }}
                            </p>
                        </div>
                    </div>

                    {{-- Tile 4: Agama / Kepercayaan --}}
                    <div class="bg-canvas-base/85 border border-white/10 rounded-xl p-4 flex flex-col justify-between hover:border-brand-gold/40 transition-colors">
                        <span class="font-mono text-[10px] font-semibold text-text-muted uppercase tracking-wider mb-2 block">
                            AGAMA / KEPERCAYAAN
                        </span>
                        <p class="font-display text-base font-bold text-text-primary">
                            {{ $student['religion'] }}
                        </p>
                    </div>

                    {{-- Tile 5: Departemen / Fakultas (Full Width 2 Kolom) --}}
                    <div class="md:col-span-2 bg-canvas-base/85 border border-white/10 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-brand-gold/40 transition-colors">
                        <div>
                            <span class="font-mono text-[10px] font-semibold text-text-muted uppercase tracking-wider mb-1 block">
                                DEPARTEMEN / FAKULTAS
                            </span>
                            <p class="font-display text-base font-bold text-text-primary">
                                S1 Teknik Komputer, Fakultas Teknik
                            </p>
                        </div>
                        <span class="font-mono text-[10px] font-bold text-brand-gold uppercase tracking-wider bg-surface-card border border-brand-gold/30 px-3 py-1.5 rounded-lg whitespace-nowrap self-start sm:self-auto">
                            KAMPUS TEMBALANG, SEMARANG
                        </span>
                    </div>

                </div>
            </div>

            {{-- ── 3. SUB-MODUL B: LIFESTYLE, HOBI & HANGOUT 2-COLUMN SPLIT ── --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">

                {{-- Left Tile: Hobi & Fokus Eksplorasi (md:col-span-6) --}}
                <div class="md:col-span-6 bg-surface-card/90 border border-brand-gold/30 rounded-xl p-6 flex flex-col justify-between gap-4 glow-gold-subtle">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-mono text-[11px] font-bold text-brand-gold uppercase tracking-wider">
                                // MINAT & AKTIVITAS
                            </span>
                            <span class="font-mono text-[10px] font-bold text-text-muted">
                                [EXP: HARDWARE]
                            </span>
                        </div>
                        <h3 class="font-display text-base sm:text-lg font-bold text-text-primary mb-3.5">
                            Hobi
                        </h3>

                        <div class="flex flex-wrap gap-2">
                            @foreach ($student['hobbies'] as $hobby)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-md font-mono text-xs font-semibold bg-canvas-base text-brand-gold border border-brand-gold/30 hover:border-brand-gold transition-colors">
                                    #{{ ucfirst($hobby) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Right Tile: Kuliner & Hangout (md:col-span-6) --}}
                <div class="md:col-span-6 bg-surface-card/90 border border-brand-red-hover/60 rounded-xl p-6 flex flex-col justify-between gap-4 glow-red-subtle">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-mono text-[10px] font-bold text-brand-gold uppercase tracking-wider">
                                // SPOT KULINER FAVORIT
                            </span>
                        </div>
                        <h3 class="font-display text-base sm:text-lg font-bold text-text-primary mb-3.5">
                            Tempat Makan Favorit
                        </h3>

                        <div class="bg-canvas-base/90 border border-white/10 rounded-xl p-3.5 sm:p-4 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <span class="size-2.5 rounded-full bg-brand-gold animate-pulse shrink-0" aria-hidden="true"></span>
                                <span class="font-display font-bold text-sm sm:text-base text-text-primary">
                                    {{ $student['favorite_food_place'] }}
                                </span>
                            </div>
                            <span class="font-mono text-[10px] font-bold px-2.5 py-1 rounded bg-brand-red text-text-primary border border-brand-red-hover whitespace-nowrap">
                                FAVORITE_SPOT
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── 4. SUB-MODUL C: SPOTIFY EMBED FULL-WIDTH ── --}}
            <div class="w-full bg-surface-card/95 text-text-primary border border-brand-gold/30 rounded-xl p-6 sm:p-7 flex flex-col justify-between overflow-hidden relative glow-gold-subtle">
                {{-- Top Dual Circuit Line --}}
                <div class="absolute inset-x-0 top-0 h-1 flex" aria-hidden="true">
                    <div class="w-1/2 bg-brand-gold"></div>
                    <div class="w-1/2 bg-brand-red"></div>
                </div>

                <div>
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="font-mono text-[11px] font-bold text-brand-gold uppercase tracking-wider">
                            // ANTHEM_TRACK // SPOTIFY_EMBED
                        </span>
                        <span class="font-mono text-[10px] bg-brand-red border border-brand-red-hover px-2.5 py-0.5 rounded text-text-primary font-semibold">
                            AUDIO_DEV_04
                        </span>
                    </div>

                    <h3 class="font-display text-base sm:text-lg font-bold text-text-primary mb-2">
                        {{ ($student['spotify_type'] ?? 'track') === 'playlist' ? 'Playlist Favorit di Spotify' : 'Lagu Favorit di Spotify' }}
                    </h3>
                </div>

                <div class="my-3">
                    @if ($student['spotify_track_id'])
                        <div class="relative min-h-[352px] overflow-hidden rounded-xl bg-canvas-base border border-white/10">
                            <div id="spotify-loading" class="absolute inset-0 z-10 grid content-center gap-4 bg-canvas-base p-5" aria-hidden="true">
                                <div class="h-4 w-2/3 animate-pulse rounded bg-brand-gold/15"></div>
                                <div class="h-3 w-1/2 animate-pulse rounded bg-white/10"></div>
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
                        <div class="rounded-xl bg-canvas-base/80 border border-white/10 p-6 text-center text-sm text-text-secondary">
                            <span class="font-mono text-[10px] uppercase tracking-widest text-brand-gold block mb-1">// STATUS: UNLINKED</span>
                            <p class="rounded-xl bg-surface-card p-2 text-sm text-text-secondary font-medium" role="status">Spotify belum ditambahkan.</p>
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-between gap-2 pt-2 border-t border-white/10 text-[10px] font-mono text-text-muted">
                    <span class="truncate">
                        Powered by Spotify API / Track ID: {{ $student['spotify_track_id'] ? Str::limit($student['spotify_track_id'], 14) : 'NULL' }}
                    </span>
                    <span class="text-brand-gold font-bold shrink-0">
                        SYNC_OK
                    </span>
                </div>
            </div>

            {{-- ── 5. MODUL BAWAH: ARCHITECTURAL METADATA BANNER ── --}}
            <div class="bg-surface-card/90 border border-white/10 rounded-xl p-3.5 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <p class="font-body text-xs text-text-secondary font-medium">
                    Data profil divalidasi oleh Biro Administrasi Akademik Mahasiswa COMETS 2026.
                </p>
                <span class="font-mono text-[10px] font-bold text-brand-gold uppercase tracking-wider self-start sm:self-auto whitespace-nowrap">
                    + CHECKSUM_VERIFIED
                </span>
            </div>

        </div>

    </div>

    {{-- ── SECTION 3: BOTTOM PREVIOUS / NEXT STUDENT PAGINATION BAR ── --}}
    <div class="flex items-center justify-between gap-4 mt-10 pt-6 border-t border-white/10">
        {{-- Previous Student Link --}}
        @if ($prevStudent)
            <a
                href="/students/{{ $prevStudent['nim'] }}"
                class="group inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-lg font-display text-xs sm:text-sm font-semibold text-text-primary bg-surface-card border border-white/15 hover:border-brand-gold hover:text-brand-gold transition-all"
                aria-label="Mahasiswa sebelumnya: {{ $prevStudent['nickname'] }}"
            >
                <span class="font-mono text-brand-gold group-hover:-translate-x-0.5 transition-transform" aria-hidden="true">&larr;</span>
                <span class="hidden sm:inline text-text-secondary">Sebelumnya:</span>
                <span class="font-bold">{{ $prevStudent['nickname'] }}</span>
            </a>
        @else
            <div></div>
        @endif

        {{-- Class Position Counter --}}
        <span class="font-mono text-[11px] sm:text-xs text-brand-gold font-semibold text-center">
            {{ $currentPosition }} / {{ $totalInClass }} (Kelas {{ $student['class_name'] }})
        </span>

        {{-- Next Student Link --}}
        @if ($nextStudent)
            <a
                href="/students/{{ $nextStudent['nim'] }}"
                class="group inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-lg font-display text-xs sm:text-sm font-semibold text-text-primary bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover transition-all glow-red-subtle"
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

{{-- ── CLIENT-SIDE INTERACTIVITY SCRIPT (Vanilla JS & Alpine Integration) ── --}}
<script>
    (() => {
        window.currentStudentPhotoMode = 'formal';

        window.setStudentPhotoMode = function (mode) {
            window.currentStudentPhotoMode = mode;

            const btnFormal = document.getElementById('btn-photo-formal');
            const btnCasual = document.getElementById('btn-photo-casual');
            const containerFormal = document.getElementById('container-photo-formal');
            const containerCasual = document.getElementById('container-photo-casual');

            const activeClasses = ['bg-brand-red', 'text-text-primary', 'font-semibold', 'border', 'border-brand-red-hover'];
            const inactiveClasses = ['text-text-secondary', 'hover:text-brand-gold'];

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
