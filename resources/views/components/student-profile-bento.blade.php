@props([
    'student',
    'prevStudent' => null,
    'nextStudent' => null,
    'currentPosition' => 1,
    'totalInClass' => 1,
])

@php
    $directoryUrl = \Illuminate\Support\Facades\Route::has('directory.index')
        ? route('directory.index')
        : '/directory';

    $prevUrl = $prevStudent
        ? (\Illuminate\Support\Facades\Route::has('students.show')
            ? route('students.show', ['nim' => $prevStudent['nim']])
            : '/students/' . $prevStudent['nim'])
        : null;

    $nextUrl = $nextStudent
        ? (\Illuminate\Support\Facades\Route::has('students.show')
            ? route('students.show', ['nim' => $nextStudent['nim']])
            : '/students/' . $nextStudent['nim'])
        : null;

    $nodeNumber = str_pad((string) $currentPosition, 2, '0', STR_PAD_LEFT);
    $hobbiesList = is_array($student['hobbies'] ?? null)
        ? $student['hobbies']
        : (is_string($student['hobbies'] ?? null)
            ? array_values(array_filter(array_map('trim', explode(',', $student['hobbies']))))
            : ['Hardware', 'Sistem Digital']);
@endphp

{{-- ==========================================================================
     PHASE 4: STUDENT PROFILE DETAIL SPLIT-SCREEN 50/50 BENTO (COMETS 2026)
     Mengacu pada .revamp-ui/PRD.md §3.4 & §4, DESIGN_TASTE.md, DESIGN_TOKENS.md
     ========================================================================== --}}
<div
    x-data="{
        photoMode: 'formal',
        copied: false,
        setMode(mode) {
            this.photoMode = mode;
            if (window.syncPhotoMode) window.syncPhotoMode(mode);
        },
        copyNim() {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText('{{ $student['nim'] }}');
            }
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        }
    }"
    class="w-full min-w-0"
>

    {{-- ── 1. NAVIGASI ATAS: BACK TO DIRECTORY, BREADCRUMB & QUICK PREV/NEXT ── --}}
    <nav
        aria-label="Navigasi profil mahasiswa"
        class="mb-6 sm:mb-8 rounded-xl border border-brand-gold/30 bg-surface-card/90 p-3.5 sm:p-4 backdrop-blur-md glow-gold-subtle"
    >
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3.5">

            {{-- Left: Back to Directory Button & Monospace Breadcrumb --}}
            <div class="flex flex-wrap items-center justify-between sm:justify-start gap-3 min-w-0">
                <a
                    href="{{ $directoryUrl }}"
                    class="inline-flex items-center gap-2 px-3.5 sm:px-4 py-2 rounded-lg bg-surface-elevated border border-white/15 hover:border-brand-gold text-xs sm:text-sm font-display font-semibold text-text-primary hover:text-brand-gold transition-all duration-200 group shrink-0"
                    aria-label="Kembali ke Direktori Mahasiswa"
                >
                    <svg class="size-4 shrink-0 stroke-[2] stroke-current text-brand-gold group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    <span>Kembali ke Direktori</span>
                </a>

                {{-- Monospace Technical Breadcrumb --}}
                <div class="font-mono text-[11px] sm:text-xs font-semibold text-text-secondary tracking-wider flex items-center gap-1.5 min-w-0 truncate">
                    <span class="text-text-muted">ROOT</span>
                    <span class="text-white/20">/</span>
                    <span class="text-text-muted">DIRECTORY</span>
                    <span class="text-white/20">/</span>
                    <span class="text-brand-gold font-bold truncate">// {{ $student['nim'] }}</span>
                </div>
            </div>

            {{-- Right: Quick Prev / Position Counter / Next Controls --}}
            <div class="flex items-center justify-between sm:justify-end gap-2 pt-2.5 lg:pt-0 border-t lg:border-t-0 border-white/10">
                {{-- Prev Student Quick Button --}}
                @if ($prevStudent && $prevUrl)
                    <a
                        href="{{ $prevUrl }}"
                        class="group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-elevated border border-white/10 hover:border-brand-gold/50 font-mono text-[11px] sm:text-xs font-semibold text-text-primary hover:text-brand-gold transition-colors"
                        aria-label="Mahasiswa sebelumnya: {{ $prevStudent['nickname'] }}"
                    >
                        <span aria-hidden="true" class="text-brand-gold group-hover:-translate-x-0.5 transition-transform">&larr;</span>
                        <span class="hidden sm:inline text-text-secondary">Prev:</span>
                        <span class="font-bold truncate max-w-[96px]">{{ $prevStudent['nickname'] }}</span>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-canvas-base/60 border border-white/5 font-mono text-[11px] sm:text-xs text-text-muted select-none cursor-not-allowed">
                        <span aria-hidden="true">&larr;</span>
                        <span>Prev</span>
                    </span>
                @endif

                {{-- Class Position Counter Badge --}}
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-canvas-base border border-brand-gold/30 font-mono text-[11px] sm:text-xs font-bold text-brand-gold whitespace-nowrap">
                    {{ $currentPosition }} / {{ $totalInClass }} (Kelas {{ $student['class_name'] }})
                </span>

                {{-- Next Student Quick Button --}}
                @if ($nextStudent && $nextUrl)
                    <a
                        href="{{ $nextUrl }}"
                        class="group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-elevated border border-white/10 hover:border-brand-gold/50 font-mono text-[11px] sm:text-xs font-semibold text-text-primary hover:text-brand-gold transition-colors"
                        aria-label="Mahasiswa berikutnya: {{ $nextStudent['nickname'] }}"
                    >
                        <span class="hidden sm:inline text-text-secondary">Next:</span>
                        <span class="font-bold truncate max-w-[96px]">{{ $nextStudent['nickname'] }}</span>
                        <span aria-hidden="true" class="text-brand-gold group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-canvas-base/60 border border-white/5 font-mono text-[11px] sm:text-xs text-text-muted select-none cursor-not-allowed">
                        <span>Next</span>
                        <span aria-hidden="true">&rarr;</span>
                    </span>
                @endif
            </div>

        </div>
    </nav>

    {{-- ── 2. SPLIT-SCREEN 50/50 DESKTOP BENTO GRID ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

        {{-- ══════════════════════════════════════════════════════════════════
             SISI KIRI (VISUAL HERO — 50% DESKTOP / lg:col-span-6)
             Foto Portrait 3:4, Toggle Formal vs Kasual, Watermark Hexagon COMETS,
             Badge Angkatan 2026, Tag Ringkas Hobi & Asal Daerah
             ══════════════════════════════════════════════════════════════════ --}}
        <section
            id="candidate-card-profile"
            aria-label="Visual hero profil mahasiswa"
            class="lg:col-span-6 bg-surface-card/95 border border-brand-gold/30 rounded-2xl p-5 sm:p-7 flex flex-col gap-5 relative overflow-hidden transition-all duration-200 glow-gold-subtle"
        >
            {{-- Top Dual-Tone Circuit Line (Tech Gold + Blood Red) --}}
            <div class="absolute inset-x-0 top-0 h-1 flex" aria-hidden="true">
                <div class="w-2/3 bg-brand-gold"></div>
                <div class="w-1/3 bg-brand-red"></div>
            </div>

            {{-- 1. Card Top Telemetry Header: // SEC_01 // CANDIDATE_CARD & [STATUS: ACTIVE_STUDENT] --}}
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-white/10 pb-3.5 pt-1">
                <span class="font-mono text-[11px] sm:text-xs font-bold uppercase tracking-wider text-brand-gold">
                    // SEC_01 // CANDIDATE_CARD
                </span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-brand-red border border-brand-red-hover font-mono text-[11px] font-bold text-text-primary glow-red-subtle">
                    <span class="size-2 rounded-full bg-brand-gold animate-pulse" aria-hidden="true"></span>
                    <span>[STATUS: ACTIVE_STUDENT]</span>
                </span>
            </div>

            {{-- 2. View Mode Switcher Bar (Alpine.js Formal vs Kasual) --}}
            <div class="flex flex-wrap items-center justify-between gap-2 p-2 px-3.5 rounded-xl bg-surface-elevated border border-white/10">
                <div class="flex items-center gap-2">
                    <span class="size-1.5 rounded-full bg-brand-gold" aria-hidden="true"></span>
                    <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-text-secondary">
                        VIEW_MODE:
                    </span>
                </div>

                <div class="inline-flex items-center p-1 rounded-lg bg-canvas-base border border-white/10" role="group" aria-label="Pilih mode foto profil">
                    {{-- Button Formal --}}
                    <button
                        type="button"
                        id="btn-photo-formal"
                        @click="setMode('formal')"
                        onclick="window.setStudentPhotoMode && window.setStudentPhotoMode('formal')"
                        :class="photoMode === 'formal' ? 'bg-brand-red text-text-primary border-brand-red-hover font-semibold glow-red-subtle' : 'text-text-secondary border-transparent hover:text-text-primary hover:bg-surface-elevated'"
                        class="photo-toggle-btn px-3.5 py-1.5 rounded-md text-xs font-mono border transition-all duration-200 flex items-center gap-1.5 cursor-pointer bg-brand-red text-text-primary border-brand-red-hover font-semibold glow-red-subtle"
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
                        :class="photoMode === 'casual' ? 'bg-brand-red text-text-primary border-brand-red-hover font-semibold glow-red-subtle' : 'text-text-secondary border-transparent hover:text-text-primary hover:bg-surface-elevated'"
                        class="photo-toggle-btn px-3.5 py-1.5 rounded-md text-xs font-mono border transition-all duration-200 flex items-center gap-1.5 cursor-pointer text-text-secondary border-transparent hover:text-text-primary hover:bg-surface-elevated"
                        data-mode="casual"
                    >
                        <span>Kasual</span>
                    </button>
                </div>
            </div>

            {{-- 3. Portrait 3:4 Geometric Futuristic Photo Frame with Circuit Accents & COMETS Hexagon Watermark --}}
            <div class="relative w-full aspect-[3/4] rounded-xl overflow-hidden border border-brand-gold/35 bg-canvas-base glow-gold-subtle">

                {{-- Futuristic Corner Circuit Framing Accents --}}
                <div class="pointer-events-none absolute inset-0 z-20" aria-hidden="true">
                    <div class="absolute top-2.5 left-2.5 size-5 border-t-2 border-l-2 border-brand-gold/80"></div>
                    <div class="absolute top-2.5 right-2.5 size-5 border-t-2 border-r-2 border-brand-gold/80"></div>
                    <div class="absolute bottom-2.5 left-2.5 size-5 border-b-2 border-l-2 border-brand-red-hover/80"></div>
                    <div class="absolute bottom-2.5 right-2.5 size-5 border-b-2 border-r-2 border-brand-gold/80"></div>
                </div>

                {{-- Top Floating Watermark Logo Hexagon COMETS & Badge Angkatan 2026 --}}
                <div class="absolute inset-x-0 top-0 z-20 flex items-center justify-between gap-2 p-3.5 sm:p-4 bg-gradient-to-b from-canvas-base/90 via-canvas-base/50 to-transparent pointer-events-none">
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-md bg-canvas-base/85 backdrop-blur-xs border border-brand-gold/40">
                        <svg class="size-4 text-brand-gold shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <polygon points="12 2 21 7 21 17 12 22 3 17 3 7 12 2" />
                        </svg>
                        <span class="font-mono text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-brand-gold">
                            COMETS // 2026
                        </span>
                    </div>

                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-brand-red/90 backdrop-blur-xs border border-brand-red-hover font-mono text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-text-primary">
                        ANGKATAN 2026
                    </span>
                </div>

                {{-- Subtle Background Hexagon COMETS Watermark Emblem --}}
                <svg class="pointer-events-none absolute right-4 bottom-14 z-15 size-24 text-brand-gold/15 select-none" viewBox="0 0 120 120" fill="none" aria-hidden="true">
                    <polygon points="60,6 108,33 108,87 60,114 12,87 12,33" stroke="currentColor" stroke-width="2" />
                    <polygon points="60,20 95,40 95,80 60,100 25,80 25,40" stroke="currentColor" stroke-width="1.25" stroke-dasharray="4 4" />
                </svg>

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
                        <div class="relative z-10 flex flex-col items-center gap-1 text-center px-4 py-2.5 rounded-lg bg-surface-card/95 border border-brand-gold/30">
                            <span class="font-mono text-xs font-bold tracking-wider text-brand-gold">[AVATAR: FORMAL]</span>
                            <span class="font-mono text-[10px] text-text-secondary uppercase tracking-widest">[X] [PHOTO_FORMAL: 3:4]</span>
                        </div>
                    </div>

                    {{-- Bottom Telemetry Overlay Bar (Formal State) --}}
                    <div class="absolute inset-x-0 bottom-0 z-20 bg-gradient-to-t from-canvas-base via-canvas-base/80 to-transparent pt-10 pb-3.5 px-4 flex items-end justify-between gap-2 pointer-events-none">
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-mono font-bold uppercase tracking-wider bg-surface-card/90 border border-brand-gold/40 text-brand-gold backdrop-blur-xs">
                            JAS ALMAMATER UNDIP
                        </span>
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-mono font-semibold uppercase tracking-wider bg-canvas-base/85 border border-white/15 text-text-secondary">
                            <span>RATIO: 3:4</span>
                            <span class="sr-only">RATIO: 1000x1000 PX</span>
                        </span>
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
                        <div class="relative z-10 flex flex-col items-center gap-1 text-center px-4 py-2.5 rounded-lg bg-surface-card/95 border border-brand-gold/30">
                            <span class="font-mono text-xs font-bold tracking-wider text-brand-gold">[AVATAR: CASUAL]</span>
                            <span class="font-mono text-[10px] text-text-secondary uppercase tracking-widest">[X] [PHOTO_CASUAL: 3:4]</span>
                        </div>
                    </div>

                    {{-- Bottom Telemetry Overlay Bar (Casual State) --}}
                    <div class="absolute inset-x-0 bottom-0 z-20 bg-gradient-to-t from-canvas-base via-canvas-base/80 to-transparent pt-10 pb-3.5 px-4 flex items-end justify-between gap-2 pointer-events-none">
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-mono font-bold uppercase tracking-wider bg-brand-red/90 border border-brand-red-hover text-text-primary backdrop-blur-xs">
                            FOTO BEBAS KASUAL
                        </span>
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-mono font-semibold uppercase tracking-wider bg-canvas-base/85 border border-white/15 text-text-secondary">
                            MODE: CASUAL_3:4
                        </span>
                    </div>
                </div>
            </div>

            {{-- 4. Tag Ringkas Sisi Kiri: Asal Daerah (origin_city) & Hobi / Minat (hobbies) --}}
            <div class="rounded-xl bg-surface-elevated/90 border border-white/10 p-4 space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="font-mono text-[10px] font-bold uppercase tracking-wider text-brand-gold">
                        // RINGKASAN_SIMPUL
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-canvas-base border border-brand-gold/30 font-mono text-xs font-semibold text-text-primary">
                        <svg class="size-3.5 text-brand-gold shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                        <span>{{ $student['origin_city'] }}</span>
                    </span>
                </div>

                {{-- Compact Hobby Chips --}}
                <div class="flex flex-wrap items-center gap-1.5">
                    @foreach ($hobbiesList as $hobby)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-canvas-base border border-white/10 font-mono text-[11px] font-medium text-text-secondary">
                            #{{ ucfirst($hobby) }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- 5. Candidate Card Footer: RECORD_HASH & CAMPUS_ID --}}
            <div class="flex flex-wrap items-center justify-between gap-2 border-t border-white/10 pt-3 text-[10px] font-mono text-text-muted">
                <span>RECORD_HASH: 0x{{ substr(md5($student['nim']), 0, 6) }}</span>
                <span class="text-brand-gold">CAMPUS_ID: UNDIP-FT-TK</span>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════════════════
             SISI KANAN (DATA & IDENTITAS PERSONAL — 50% DESKTOP / lg:col-span-6)
             > STUDENT_NODE_XX, Nama Lengkap & Panggilan, Copy NIM, Personal Quote
             Border Kiri Blood Red, Biodata Resmi, Hobi, Kuliner, Spotify & Instagram
             ══════════════════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-6 flex flex-col gap-6 min-w-0">

            {{-- ── PANEL 1: CORE IDENTITY, NIM CLIPBOARD, QUOTE & INSTAGRAM ── --}}
            <section
                aria-label="Identitas utama dan kutipan personal"
                class="relative rounded-2xl border border-brand-gold/30 bg-surface-card/95 p-5 sm:p-7 space-y-5 overflow-hidden glow-gold-subtle"
            >
                {{-- Top Dual-Tone Circuit Line --}}
                <div class="absolute inset-x-0 top-0 h-1 flex" aria-hidden="true">
                    <div class="w-2/3 bg-brand-gold"></div>
                    <div class="w-1/3 bg-brand-red"></div>
                </div>

                {{-- Terminal Node Tag & Class Badge --}}
                <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-canvas-base border border-brand-gold/40 font-mono text-xs font-bold uppercase tracking-wider text-brand-gold">
                        <span class="size-2 rounded-full bg-brand-gold-glow animate-pulse" aria-hidden="true"></span>
                        <span>&gt; STUDENT_NODE_{{ $nodeNumber }}</span>
                    </span>

                    <span class="inline-flex items-center px-3 py-1 rounded-md bg-brand-red border border-brand-red-hover font-mono text-xs font-bold uppercase tracking-wider text-text-primary glow-red-subtle">
                        KELAS {{ $student['class_name'] }} // TEKKOM_26
                    </span>
                </div>

                {{-- Full Name (font-display besar) & Nickname --}}
                <div class="space-y-2">
                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                        <span class="font-mono text-xs uppercase tracking-wider text-text-secondary">
                            PANGGILAN: <strong class="font-display text-base sm:text-lg text-brand-gold font-bold">{{ $student['nickname'] }}</strong>
                        </span>
                    </div>

                    <h1 class="font-display text-2xl sm:text-4xl font-bold text-text-primary tracking-tight leading-tight break-words">
                        {{ $student['name'] }}
                    </h1>
                </div>

                {{-- Monospace NIM Box with Alpine.js Copy Clipboard Button & Social Instagram Link --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
                    <div class="inline-flex items-center justify-between sm:justify-start gap-3 px-3.5 py-2.5 rounded-xl bg-surface-elevated border border-white/15 font-mono text-xs sm:text-sm text-text-primary font-bold">
                        <span class="text-brand-gold">NIM: {{ $student['nim'] }}</span>
                        <button
                            type="button"
                            @click="copyNim()"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-canvas-base border border-white/10 hover:border-brand-gold text-text-secondary hover:text-brand-gold transition-colors cursor-pointer"
                            :title="copied ? 'Tersalin!' : 'Salin NIM'"
                            aria-label="Salin NIM ke clipboard"
                        >
                            <template x-if="!copied">
                                <svg class="size-3.5 stroke-[2]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                </svg>
                            </template>
                            <template x-if="copied">
                                <svg class="size-3.5 stroke-[2] text-brand-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                </svg>
                            </template>
                            <span class="text-[10px] font-mono uppercase tracking-wider" x-text="copied ? 'Tersalin!' : 'Salin'">Salin</span>
                        </button>
                    </div>

                    {{-- Instagram Social Action Button --}}
                    <a
                        href="{{ $student['instagram_url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary font-display text-xs sm:text-sm font-semibold transition-all duration-200 glow-red-subtle group"
                    >
                        <svg class="size-4 shrink-0 text-brand-gold" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                        <span>{{ '@' . $student['instagram_username'] }}</span>
                        <span class="font-mono text-xs text-brand-gold transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true">&#8599;</span>
                    </a>
                </div>

                {{-- Personal Quote Callout Box with Blood Red Left Border Accent (border-l-4 border-l-brand-red) --}}
                <div class="bg-canvas-base/90 border border-white/10 border-l-4 border-l-brand-red p-4 sm:p-5 rounded-r-xl">
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="font-mono text-[10px] font-bold uppercase tracking-wider text-brand-gold">
                            // ASPIRASI & PRINSIP
                        </span>
                        <span class="font-mono text-[10px] uppercase tracking-wider text-text-muted">
                            REV_01
                        </span>
                    </div>
                    <blockquote class="italic font-body text-sm sm:text-base font-medium leading-relaxed text-text-primary">
                        “{{ $student['bio_quote'] }}”
                    </blockquote>
                </div>
            </section>

            {{-- ── PANEL 2: INFORMASI IDENTITAS & KEPENDUDUKAN (StudentController@show Schema) ── --}}
            <section
                aria-label="Informasi identitas dan kependudukan"
                class="bg-surface-card/95 border border-white/10 rounded-2xl p-5 sm:p-7"
            >
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 pb-4 border-b border-white/10">
                    <div>
                        <span class="font-mono text-[11px] font-bold text-brand-gold uppercase tracking-wider block">
                            // 01_BIODATA_RESMI
                        </span>
                        <h2 class="font-display text-xl sm:text-2xl font-bold text-text-primary tracking-tight mt-0.5">
                            Informasi Identitas & Kependudukan
                        </h2>
                    </div>
                    <span class="inline-flex items-center self-start sm:self-auto px-3 py-1.5 rounded-lg bg-surface-elevated border border-brand-gold/30 font-mono text-[11px] font-bold text-brand-gold tracking-wider">
                        VERIFIKASI_FAKULTAS: OK
                    </span>
                </div>

                {{-- Structured 5-Tile Metadata Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    {{-- Tile 1: Asal Kota / Daerah (with #DOMISILI) --}}
                    <div class="bg-surface-elevated/90 border border-white/10 rounded-xl p-4 flex flex-col justify-between hover:border-brand-gold/40 transition-colors">
                        <span class="font-mono text-[10px] font-semibold text-text-secondary uppercase tracking-wider mb-2 block">
                            ASAL KOTA / DAERAH
                        </span>
                        <div class="flex items-baseline justify-between gap-2">
                            <p class="font-display text-base font-bold text-text-primary">
                                {{ $student['origin_city'] }}
                            </p>
                            <span class="font-mono text-[10px] text-brand-gold font-semibold bg-brand-gold/10 border border-brand-gold/30 px-2 py-0.5 rounded">
                                #DOMISILI
                            </span>
                        </div>
                    </div>

                    {{-- Tile 2: Tempat, Tanggal Lahir --}}
                    <div class="bg-surface-elevated/90 border border-white/10 rounded-xl p-4 flex flex-col justify-between hover:border-brand-gold/40 transition-colors">
                        <span class="font-mono text-[10px] font-semibold text-text-secondary uppercase tracking-wider mb-2 block">
                            TEMPAT, TANGGAL LAHIR
                        </span>
                        <p class="font-display text-base font-bold text-text-primary leading-snug">
                            {{ $student['birth_place'] }}, {{ $student['birth_date'] }}
                        </p>
                    </div>

                    {{-- Tile 3: Usia Saat Ini (with GENERASI 2026 badge) --}}
                    <div class="bg-surface-elevated/90 border border-white/10 rounded-xl p-4 flex flex-col justify-between hover:border-brand-gold/40 transition-colors">
                        <span class="font-mono text-[10px] font-semibold text-text-secondary uppercase tracking-wider mb-2 block">
                            USIA SAAT INI
                        </span>
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-display text-base font-bold text-text-primary">
                                {{ $student['age'] ? $student['age'].' Tahun' : 'Tidak tersedia' }}
                            </p>
                            <span class="font-mono text-[10px] font-bold px-2.5 py-1 rounded bg-brand-red text-text-primary border border-brand-red-hover">
                                GENERASI 2026
                            </span>
                        </div>
                    </div>

                    {{-- Tile 4: Agama / Kepercayaan --}}
                    <div class="bg-surface-elevated/90 border border-white/10 rounded-xl p-4 flex flex-col justify-between hover:border-brand-gold/40 transition-colors">
                        <span class="font-mono text-[10px] font-semibold text-text-secondary uppercase tracking-wider mb-2 block">
                            AGAMA / KEPERCAYAAN
                        </span>
                        <p class="font-display text-base font-bold text-text-primary">
                            {{ $student['religion'] }}
                        </p>
                    </div>

                    {{-- Tile 5: Departemen / Fakultas (Full Width 2 Kolom) with KAMPUS TEMBALANG --}}
                    <div class="sm:col-span-2 bg-surface-elevated/90 border border-white/10 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:border-brand-gold/40 transition-colors">
                        <div>
                            <span class="font-mono text-[10px] font-semibold text-text-secondary uppercase tracking-wider mb-1 block">
                                DEPARTEMEN / FAKULTAS
                            </span>
                            <p class="font-display text-base font-bold text-text-primary">
                                S1 Teknik Komputer, Fakultas Teknik
                            </p>
                        </div>
                        <span class="font-mono text-[10px] font-bold text-brand-gold uppercase tracking-wider bg-canvas-base border border-brand-gold/30 px-3 py-1.5 rounded-lg whitespace-nowrap self-start sm:self-auto">
                            KAMPUS TEMBALANG, SEMARANG
                        </span>
                    </div>

                </div>
            </section>

            {{-- ── PANEL 3: HOBI & TEMPAT MAKAN FAVORIT 2-COLUMN SPLIT ── --}}
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">

                {{-- Left Tile: Hobi & Fokus Eksplorasi (md:col-span-6) --}}
                <section
                    aria-label="Hobi dan minat mahasiswa"
                    class="md:col-span-6 bg-surface-card/95 border border-white/10 rounded-2xl p-5 sm:p-6 flex flex-col justify-between gap-4"
                >
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-mono text-[11px] font-bold text-brand-gold uppercase tracking-wider">
                                // MINAT & AKTIVITAS
                            </span>
                            <span class="font-mono text-[10px] font-bold text-text-secondary">
                                [EXP: HARDWARE]
                            </span>
                        </div>
                        <h3 class="font-display text-base sm:text-lg font-bold text-text-primary mb-3.5">
                            Hobi & Fokus Eksplorasi
                        </h3>

                        <div class="flex flex-wrap gap-2">
                            @foreach ($hobbiesList as $hobby)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg font-mono text-xs font-semibold bg-surface-elevated text-text-primary border border-brand-gold/30 hover:border-brand-gold transition-colors">
                                    #{{ ucfirst($hobby) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- Right Tile: Tempat Makan Favorit (md:col-span-6) --}}
                <section
                    aria-label="Tempat makan favorit mahasiswa"
                    class="md:col-span-6 bg-surface-card/95 border border-white/10 rounded-2xl p-5 sm:p-6 flex flex-col justify-between gap-4"
                >
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="font-mono text-[10px] font-bold text-brand-gold uppercase tracking-wider">
                                // SPOT KULINER FAVORIT
                            </span>
                            <span class="font-mono text-[10px] text-text-muted uppercase tracking-wider">
                                REKOMENDASI_MAHASISWA
                            </span>
                        </div>
                        <h3 class="font-display text-base sm:text-lg font-bold text-text-primary mb-3.5">
                            Tempat Makan Favorit
                        </h3>

                        <div class="bg-surface-elevated border border-white/10 rounded-xl p-3.5 sm:p-4 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="size-2.5 rounded-full bg-brand-gold shrink-0 animate-pulse" aria-hidden="true"></span>
                                <span class="font-display font-bold text-sm sm:text-base text-text-primary truncate">
                                    {{ $student['favorite_food_place'] }}
                                </span>
                            </div>
                            <span class="font-mono text-[10px] font-bold px-2.5 py-1 rounded bg-brand-gold/15 text-brand-gold border border-brand-gold/40 whitespace-nowrap shrink-0">
                                FAVORITE_SPOT
                            </span>
                        </div>
                    </div>
                </section>

            </div>

            {{-- ── PANEL 4: SPOTIFY EMBED PLAYER ── --}}
            <section
                aria-label="Pemutar musik Spotify favorit"
                class="w-full bg-surface-card/95 text-text-primary border border-brand-gold/30 rounded-2xl p-5 sm:p-7 flex flex-col justify-between overflow-hidden relative glow-gold-subtle"
            >
                {{-- Top Dual-Tone Circuit Line --}}
                <div class="absolute inset-x-0 top-0 h-1 flex" aria-hidden="true">
                    <div class="w-1/2 bg-brand-red"></div>
                    <div class="w-1/2 bg-brand-gold"></div>
                </div>

                {{-- Decorative Crosshair Elements --}}
                <span class="absolute top-3 right-3.5 text-white/20 font-mono text-xs select-none pointer-events-none" aria-hidden="true">+</span>
                <span class="absolute bottom-3 left-3.5 text-white/20 font-mono text-xs select-none pointer-events-none" aria-hidden="true">+</span>

                {{-- Header Spotify Box --}}
                <div class="pt-1">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="font-mono text-[11px] font-bold text-brand-gold uppercase tracking-wider">
                            // ANTHEM_TRACK // SPOTIFY_EMBED
                        </span>
                        <span class="font-mono text-[10px] bg-surface-elevated border border-white/15 px-2.5 py-0.5 rounded text-text-secondary">
                            AUDIO_DEV_04
                        </span>
                    </div>

                    <h3 class="font-display text-base sm:text-lg font-bold text-text-primary mb-2">
                        {{ ($student['spotify_type'] ?? 'track') === 'playlist' ? 'Playlist Favorit di Spotify' : 'Lagu Favorit di Spotify' }}
                    </h3>
                </div>

                {{-- Embedded Spotify Player --}}
                <div class="my-3">
                    @if ($student['spotify_track_id'])
                        <div class="relative min-h-[352px] overflow-hidden rounded-xl bg-canvas-base border border-white/10">
                            <div id="spotify-loading" class="absolute inset-0 z-10 grid content-center gap-4 bg-surface-elevated p-5" aria-hidden="true">
                                <div class="h-4 w-2/3 animate-pulse rounded bg-white/15"></div>
                                <div class="h-3 w-1/2 animate-pulse rounded bg-white/10"></div>
                                <div class="h-10 w-full animate-pulse rounded-lg bg-white/10"></div>
                            </div>
                            <p id="spotify-status" class="sr-only" role="status" aria-live="polite">Memuat pemutar Spotify.</p>
                            <iframe
                                id="spotify-embed"
                                data-testid="embed-iframe"
                                title="{{ ($student['spotify_type'] ?? 'track') === 'playlist' ? 'Playlist' : 'Lagu' }} favorit {{ $student['nickname'] }} di Spotify"
                                class="rounded-xl"
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
                        <div class="rounded-xl bg-canvas-base border border-white/10 p-6 text-center text-sm text-text-secondary">
                            <span class="font-mono text-[10px] uppercase tracking-widest text-brand-gold block mb-1">// STATUS: UNLINKED</span>
                            <p class="rounded-xl bg-surface-elevated p-2.5 text-sm text-text-primary font-medium" role="status">Spotify belum ditambahkan.</p>
                        </div>
                    @endif
                </div>

                {{-- Technical Spotify Footer --}}
                <div class="flex items-center justify-between gap-2 pt-2.5 border-t border-white/10 text-[10px] font-mono text-text-muted">
                    <span class="truncate">
                        Powered by Spotify API / Track ID: {{ $student['spotify_track_id'] ? Str::limit($student['spotify_track_id'], 14) : 'NULL' }}
                    </span>
                    <span class="text-brand-gold font-bold shrink-0">
                        SYNC_OK
                    </span>
                </div>
            </section>

            {{-- ── PANEL 5: VERIFICATION STRIP ── --}}
            <div class="bg-surface-card/90 border border-white/10 rounded-xl p-3.5 sm:p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                <p class="font-body text-xs text-text-secondary font-medium">
                    Data profil divalidasi oleh Biro Administrasi Akademik Mahasiswa.
                </p>
                <span class="font-mono text-[10px] font-bold text-brand-gold uppercase tracking-wider self-start sm:self-auto whitespace-nowrap">
                    + CHECKSUM_VERIFIED
                </span>
            </div>

        </div>

    </div>

    {{-- ── 3. BOTTOM PREVIOUS / NEXT STUDENT PAGINATION BAR ── --}}
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-10 pt-6 border-t border-white/10">
        {{-- Previous Student Link --}}
        @if ($prevStudent && $prevUrl)
            <a
                href="{{ $prevUrl }}"
                class="group inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-display font-semibold text-text-primary bg-surface-card border border-white/15 hover:border-brand-gold hover:text-brand-gold transition-all"
                aria-label="Mahasiswa sebelumnya: {{ $prevStudent['nickname'] }}"
            >
                <svg class="size-4 shrink-0 stroke-[2] stroke-current text-brand-gold group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span class="text-text-secondary">Sebelumnya:</span>
                <span class="font-bold">{{ $prevStudent['nickname'] }}</span>
            </a>
        @else
            <div class="hidden sm:block"></div>
        @endif

        {{-- Class Position Counter --}}
        <span class="font-mono text-xs text-text-secondary font-semibold text-center">
            POSISI KELAS: <strong class="text-brand-gold">{{ $currentPosition }} / {{ $totalInClass }}</strong> (Kelas {{ $student['class_name'] }})
        </span>

        {{-- Next Student Link --}}
        @if ($nextStudent && $nextUrl)
            <a
                href="{{ $nextUrl }}"
                class="group inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-display font-semibold text-text-primary bg-surface-card border border-white/15 hover:border-brand-gold hover:text-brand-gold transition-all"
                aria-label="Mahasiswa berikutnya: {{ $nextStudent['nickname'] }}"
            >
                <span class="text-text-secondary">Selanjutnya:</span>
                <span class="font-bold">{{ $nextStudent['nickname'] }}</span>
                <svg class="size-4 shrink-0 stroke-[2] stroke-current text-brand-gold group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        @else
            <div class="hidden sm:block"></div>
        @endif
    </div>

</div>

{{-- ── 4. CLIENT-SIDE INTERACTIVITY SCRIPT (Vanilla JS & Alpine Integration) ── --}}
<script>
    (() => {
        window.currentStudentPhotoMode = 'formal';

        window.setStudentPhotoMode = function (mode) {
            window.currentStudentPhotoMode = mode;

            const btnFormal = document.getElementById('btn-photo-formal');
            const btnCasual = document.getElementById('btn-photo-casual');
            const containerFormal = document.getElementById('container-photo-formal');
            const containerCasual = document.getElementById('container-photo-casual');

            const activeClasses = ['bg-brand-red', 'text-text-primary', 'border-brand-red-hover', 'font-semibold', 'glow-red-subtle'];
            const inactiveClasses = ['text-text-secondary', 'border-transparent', 'hover:text-text-primary', 'hover:bg-surface-elevated'];

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
