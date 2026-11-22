@props([
    'student' => [],
    'index' => 1,
    'locked' => false,
    'avatarPlaceholder' => '/assets/Placeholder.png',
])

@php
    $nim = $student['nim'] ?? '21120126000000';
    $nickname = $student['nickname'] ?? $student['name'] ?? 'Mahasiswa';
    $fullName = $student['name'] ?? $nickname;
    $className = $student['class_name'] ?? 'A';
    $originCity = $student['origin_city'] ?? 'Semarang';
    $hobbies = $student['hobbies'] ?? ['Hardware', 'Sistem Digital'];
    if (is_string($hobbies)) {
        $hobbies = array_values(array_filter(array_map('trim', explode(',', $hobbies))));
    }
    $avatarUrl = ! empty($student['formal_photo_url'])
        ? $student['formal_photo_url']
        : (! empty($student['avatar_url']) ? $student['avatar_url'] : asset($avatarPlaceholder));
    $formattedIndex = '#TK26-' . str_pad((string) $index, 3, '0', STR_PAD_LEFT);
    $profileUrl = \Illuminate\Support\Facades\Route::has('students.show')
        ? route('students.show', ['nim' => $nim])
        : '/students/' . $nim;
    $unlockUrl = \Illuminate\Support\Facades\Route::has('unlock')
        ? route('unlock')
        : '/unlock';
@endphp

{{-- ==========================================================================
     STUDENT DIRECTORY CARD COMPONENT (3:4 EDITORIAL PORTRAIT — COMETS 2026)
     Mengacu pada .revamp-ui/PRD.md §3.3.2 & DESIGN_TASTE.md §2.C (The Future of Experience)
     ========================================================================== --}}
<article
    data-student-card
    data-class="{{ $className }}"
    class="group relative flex flex-col justify-between rounded-xl border border-white/10 hover:border-brand-gold bg-surface-card/95 overflow-hidden transition-all duration-300 glow-gold-subtle"
>
    {{-- Top Dual-Tone Circuit Line --}}
    <div class="h-1 w-full flex" aria-hidden="true">
        <div class="w-2/3 bg-brand-gold/60 group-hover:bg-brand-gold transition-colors"></div>
        <div class="w-1/3 bg-brand-red group-hover:bg-brand-red-hover transition-colors"></div>
    </div>

    {{-- 1. Portrait 3:4 Photo Container (Monochrome Default -> Full Color & Gold Glow on Hover) --}}
    <div class="relative w-full aspect-[3/4] overflow-hidden bg-canvas-base border-b border-white/10">

        {{-- Top Floating Telemetry Overlay Bar --}}
        <div class="absolute inset-x-0 top-0 z-20 flex items-center justify-between gap-2 p-3.5">
            <span class="px-2.5 py-1 rounded-md bg-canvas-base/85 backdrop-blur-xs border border-white/15 font-mono text-xs font-bold uppercase tracking-wider text-brand-gold">
                {{ $formattedIndex }}
            </span>

            @if ($locked)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-brand-red border border-brand-red-hover text-text-primary font-mono text-[10px] font-bold uppercase tracking-wider">
                    <span class="size-1.5 rounded-full bg-brand-gold animate-pulse" aria-hidden="true"></span>
                    <span>[PREVIEW: LOCKED]</span>
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-brand-red/90 backdrop-blur-xs border border-brand-red-hover font-mono text-[11px] font-bold uppercase tracking-wider text-text-primary">
                    KELAS {{ $className }}
                </span>
            @endif
        </div>

        @if ($locked)
            {{-- Guest Locked Preview State --}}
            <div class="w-full h-full flex flex-col items-center justify-center p-6 text-center bg-canvas-base/90">
                <span class="font-mono text-[10px] uppercase tracking-wider text-brand-gold mb-3">
                    [X] [AVATAR: 1:1 — LOCKED]
                </span>
                <div class="size-16 rounded-xl border border-brand-gold/30 bg-surface-card flex items-center justify-center mb-3">
                    <svg class="size-7 stroke-[1.75] stroke-current text-brand-gold" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <span class="font-mono text-[10px] uppercase tracking-wider text-text-muted">
                    3:4 PORTRAIT // ENCRYPTED
                </span>
            </div>
        @else
            {{-- Student Portrait Image: Monochrome Default -> Full Color on Hover --}}
            <img
                src="{{ $avatarUrl }}"
                alt="Foto {{ $nickname }}"
                class="w-full h-full object-cover grayscale contrast-105 group-hover:grayscale-0 group-hover:scale-105 transition-all duration-300"
                loading="lazy"
                onerror="this.onerror=null; this.src='{{ asset($avatarPlaceholder) }}';"
            />
        @endif

        {{-- Bottom Gradient Vignette & NIM / Domisili Overlay --}}
        <div class="absolute inset-x-0 bottom-0 z-20 bg-gradient-to-t from-canvas-base via-canvas-base/85 to-transparent pt-10 pb-3.5 px-4 flex items-end justify-between gap-2">
            <span class="inline-flex items-center px-2.5 py-1 rounded bg-surface-card/90 border border-brand-gold/30 font-mono text-[11px] font-semibold text-brand-gold tracking-wider">
                {{ $locked ? 'NIM // 21120126••••••' : 'NIM // ' . $nim }}
            </span>

            <span class="inline-flex items-center gap-1 font-mono text-[11px] font-medium text-text-secondary truncate max-w-[140px]">
                <svg class="size-3.5 shrink-0 stroke-[1.75] stroke-current text-brand-gold" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
                <span class="truncate">{{ $locked ? 'Kota Disamarkan' : $originCity }}</span>
            </span>
        </div>
    </div>

    {{-- 2. Editorial Identity Metadata & Action Footer --}}
    <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between gap-4">
        <div>
            {{-- Nickname & Full Name --}}
            <h2 class="font-display text-xl sm:text-2xl font-bold text-text-primary group-hover:text-brand-gold tracking-tight leading-snug truncate transition-colors">
                {{ $nickname }}
            </h2>
            <p class="font-body text-xs sm:text-sm font-medium text-text-secondary leading-snug line-clamp-1 mt-0.5" title="{{ $fullName }}">
                {{ $locked ? 'Nama Lengkap Disamarkan' : $fullName }}
            </p>

            {{-- Hobbies / Interest Badges --}}
            <div class="mt-3.5 flex flex-wrap items-center gap-1.5 min-h-[28px]">
                @if ($locked)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-canvas-base border border-white/10 font-mono text-[10px] uppercase tracking-wider text-text-muted">
                        [DATA_PRIVAT_TERKUNCI]
                    </span>
                @else
                    @php
                        $displayedHobbies = array_slice($hobbies, 0, 2);
                        $remainingCount = count($hobbies) - count($displayedHobbies);
                    @endphp
                    @foreach ($displayedHobbies as $tag)
                        <span
                            class="inline-flex items-center max-w-[130px] px-2.5 py-1 rounded-md bg-surface-elevated border border-white/10 group-hover:border-brand-gold/30 font-mono text-[10px] font-medium uppercase tracking-wider text-text-secondary truncate transition-colors"
                            title="#{{ $tag }}"
                        >
                            #{{ $tag }}
                        </span>
                    @endforeach
                    @if ($remainingCount > 0)
                        <span
                            class="inline-flex items-center px-2 py-1 rounded-md bg-brand-gold/10 border border-brand-gold/30 font-mono text-[10px] font-semibold text-brand-gold shrink-0"
                            title="Hobi lainnya: {{ implode(', ', array_slice($hobbies, 2)) }}"
                        >
                            +{{ $remainingCount }}
                        </span>
                    @endif
                @endif
            </div>
        </div>

        {{-- Bottom Action Row (Entire Card Clickable via after:inset-0) --}}
        <div class="pt-3 border-t border-white/10">
            @if ($locked)
                <a
                    href="{{ $unlockUrl }}"
                    class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg bg-brand-red hover:bg-brand-red-hover text-text-primary font-mono text-xs font-bold uppercase tracking-wider border border-brand-red-hover transition-colors after:absolute after:inset-0 after:z-30"
                >
                    <svg class="size-3.5 shrink-0 stroke-[2] stroke-current text-brand-gold" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    <span>[Terkunci // Butuh Passcode]</span>
                </a>
            @else
                <a
                    href="{{ $profileUrl }}"
                    class="inline-flex items-center justify-between w-full px-4 py-2.5 rounded-lg bg-surface-elevated group-hover:bg-brand-red text-text-primary font-display font-semibold text-xs sm:text-sm border border-white/10 group-hover:border-brand-red-hover transition-colors after:absolute after:inset-0 after:z-30"
                >
                    <span>Lihat Profil</span>
                    <span class="font-mono text-sm text-brand-gold group-hover:text-text-primary leading-none transition-colors" aria-hidden="true">&rarr;</span>
                </a>
            @endif
        </div>
    </div>
</article>
