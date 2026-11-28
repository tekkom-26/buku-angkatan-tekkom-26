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
    $rawPhotoUrl = ! empty($student['formal_photo_url'])
        ? $student['formal_photo_url']
        : ($student['avatar_url'] ?? '');
    $isPlaceholderPhoto = empty($rawPhotoUrl) || str_contains((string) $rawPhotoUrl, 'Placeholder.png');
    $formattedIndex = '#TK26-' . str_pad((string) $index, 3, '0', STR_PAD_LEFT);
    $profileUrl = \Illuminate\Support\Facades\Route::has('students.show')
        ? route('students.show', ['nim' => $nim])
        : '/students/' . $nim;
    $unlockUrl = \Illuminate\Support\Facades\Route::has('unlock')
        ? route('unlock')
        : '/unlock';
@endphp

<article
    data-student-card
    data-class="{{ $className }}"
    class="group relative flex flex-col justify-between rounded-2xl border border-white/10 hover:border-brand-gold/60 bg-surface-card/90 overflow-hidden transition-colors duration-200"
>
    {{-- 1. Portrait 3:4 Photo Container --}}
    <div class="relative w-full aspect-[3/4] overflow-hidden bg-canvas-base border-b border-white/10">

        {{-- Top Floating Index & Class Badge --}}
        <div class="absolute inset-x-0 top-0 z-20 flex items-center justify-between gap-2 p-3.5">
            <span class="px-2.5 py-1 rounded-lg bg-canvas-base/85 backdrop-blur-xs border border-white/15 font-mono text-xs font-semibold text-brand-gold">
                {{ $formattedIndex }}
            </span>

            @if ($locked)
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-brand-red border border-brand-red-hover text-text-primary font-display text-xs font-semibold">
                    Terkunci
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-brand-red/90 backdrop-blur-xs border border-brand-red-hover font-display text-xs font-semibold text-text-primary">
                    Kelas {{ $className }}
                </span>
            @endif
        </div>

        @if ($locked)
            <div class="w-full h-full flex flex-col items-center justify-center p-6 text-center bg-canvas-base/90">
                <div class="size-16 rounded-2xl border border-brand-gold/30 bg-surface-card flex items-center justify-center mb-3">
                    <svg class="size-7 stroke-[1.75] stroke-current text-brand-gold" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <span class="font-display text-xs text-text-muted">
                    Foto Profil Terkunci
                </span>
            </div>
        @elseif ($isPlaceholderPhoto)
            <x-wireframe-slot variant="emblem" :label="$nickname" hint="Belum ada foto profil" />
        @else
            <img
                src="{{ $rawPhotoUrl }}"
                alt="Foto {{ $nickname }}"
                class="w-full h-full object-cover grayscale contrast-105 group-hover:grayscale-0 group-hover:scale-105 transition-all duration-300"
                loading="lazy"
            />
        @endif

        {{-- Bottom Gradient Vignette & NIM / MBTI / Domisili --}}
        <div class="absolute inset-x-0 bottom-0 z-20 bg-gradient-to-t from-canvas-base via-canvas-base/85 to-transparent pt-12 pb-3.5 px-4 space-y-1.5">
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-surface-card/90 border border-white/15 font-mono text-[11px] font-semibold text-brand-gold tracking-wide">
                    {{ $locked ? '21120126••••••' : $nim }}
                </span>
                @if (! $locked && ! empty($student['mbti_code']))
                    <span
                        data-mbti-badge
                        class="inline-flex items-center px-2 py-0.5 rounded bg-brand-gold/15 border border-brand-gold/45 font-mono text-[11px] font-bold text-brand-gold tracking-wider"
                    >
                        {{ $student['mbti_code'] }}
                    </span>
                @endif
            </div>
            <div class="flex items-center gap-1.5 font-body text-xs text-text-secondary" title="{{ $originCity }}">
                <svg class="size-3.5 shrink-0 stroke-[1.75] stroke-current text-brand-gold" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
                <span class="line-clamp-1">{{ $locked ? 'Kota Disamarkan' : $originCity }}</span>
            </div>
        </div>
    </div>

    {{-- 2. Editorial Identity Metadata & Action Footer --}}
    <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between gap-4">
        <div>
            <h2 class="font-display text-xl sm:text-2xl font-bold text-text-primary group-hover:text-brand-gold tracking-tight leading-snug line-clamp-1 transition-colors">
                {{ $nickname }}
            </h2>
            <p class="font-body text-xs sm:text-sm font-medium text-text-secondary leading-snug line-clamp-1 mt-0.5" title="{{ $fullName }}">
                {{ $locked ? 'Nama Lengkap Disamarkan' : $fullName }}
            </p>

            {{-- Hobbies Badges --}}
            <div class="mt-3.5 flex flex-wrap items-center gap-1.5 min-h-[28px]">
                @if ($locked)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-canvas-base border border-white/10 font-body text-xs text-text-muted">
                        Data Privat Terkunci
                    </span>
                @else
                    @php
                        $displayedHobbies = array_slice($hobbies, 0, 2);
                        $remainingCount = count($hobbies) - count($displayedHobbies);
                    @endphp
                    @foreach ($displayedHobbies as $tag)
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-lg bg-surface-elevated border border-white/10 font-body text-xs text-text-secondary"
                            title="{{ $tag }}"
                        >
                            #{{ \Illuminate\Support\Str::limit($tag, 18) }}
                        </span>
                    @endforeach
                    @if ($remainingCount > 0)
                        <span
                            class="inline-flex items-center px-2 py-1 rounded-lg bg-brand-gold/10 border border-brand-gold/30 font-mono text-xs font-semibold text-brand-gold shrink-0"
                            title="Hobi lainnya: {{ implode(', ', array_slice($hobbies, 2)) }}"
                        >
                            +{{ $remainingCount }}
                        </span>
                    @endif
                @endif
            </div>
        </div>

        {{-- Bottom Action Row (>= 44px touch height) --}}
        <div class="pt-3 border-t border-white/10">
            @if ($locked)
                <a
                    href="{{ $unlockUrl }}"
                    class="inline-flex items-center justify-center gap-2 w-full min-h-11 px-4 py-2.5 rounded-xl bg-brand-red hover:bg-brand-red-hover text-text-primary font-display text-xs sm:text-sm font-semibold border border-brand-red-hover transition-colors after:absolute after:inset-0 after:z-30"
                >
                    <svg class="size-3.5 shrink-0 stroke-[2] stroke-current text-brand-gold" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    <span>Masuk untuk Melihat</span>
                </a>
            @else
                <a
                    href="{{ $profileUrl }}"
                    class="inline-flex items-center justify-between w-full min-h-11 px-4 py-2.5 rounded-xl bg-surface-elevated group-hover:bg-brand-red text-text-primary font-display font-semibold text-xs sm:text-sm border border-white/10 group-hover:border-brand-red-hover transition-colors after:absolute after:inset-0 after:z-30"
                >
                    <span>Lihat Profil</span>
                    <span class="font-mono text-sm text-brand-gold group-hover:text-text-primary leading-none transition-colors" aria-hidden="true">&rarr;</span>
                </a>
            @endif
        </div>
    </div>
</article>
