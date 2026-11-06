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
     STUDENT DIRECTORY CARD COMPONENT (Unlocked & Guest Locked States)
     Mengacu pada GitHub Issue #14, DESIGN.md, dan DOCS_COLOR_CONVENTION.md
     Figma Reference: Screen 3 Node 1:854 (Avatars 1:926, 1:969, 1:1012)
     ========================================================================== --}}
<article
    data-student-card
    data-class="{{ $className }}"
    class="group relative flex flex-col justify-between rounded-2xl border border-surface-border bg-surface-card p-5 sm:p-6 shadow-xs hover:border-primary-900/30 transition-all duration-180"
>
    <div>
        {{-- Top Metadata Bar: Monospace Index & Class Tag --}}
        <div class="flex items-center justify-between gap-2 pb-4 mb-4 border-b border-surface-border">
            <span class="font-mono text-xs font-bold uppercase tracking-[0.05em] text-primary-700">
                {{ $formattedIndex }}
            </span>

            @if ($locked)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-highlight-500 text-highlight-900 font-mono text-[10px] font-bold uppercase tracking-wider">
                    <span class="size-1.5 rounded-full bg-highlight-900" aria-hidden="true"></span>
                    [PREVIEW: LOCKED]
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-white border border-surface-border font-mono text-[11px] font-bold uppercase tracking-wider text-primary-900">
                    KELAS {{ $className }}
                </span>
            @endif
        </div>

        {{-- Profile Header Row: 100x100px 1:1 Avatar Frame + Identity Block --}}
        <div class="flex items-start gap-4 mb-4">

            {{-- 1:1 Ratio 100x100px Rounded-xl Avatar Wireframe Container --}}
            <div class="relative size-[100px] shrink-0 aspect-square rounded-xl border border-surface-border bg-surface-canvas p-2 flex flex-col items-center justify-between overflow-hidden">
                @if ($locked)
                    <span class="w-full text-left font-mono text-[7px] uppercase tracking-wider text-primary-500 leading-tight">
                        [X] [AVATAR: 1:1 — LOCKED]
                    </span>
                    <div class="flex-1 flex items-center justify-center">
                        {{-- Monoline Lock SVG Icon --}}
                        <svg class="size-7 stroke-[1.75] stroke-current text-primary-500" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>
                    <span class="w-full text-right font-mono text-[7px] uppercase tracking-wider text-primary-500">
                        100x100
                    </span>
                @else
                    <span class="w-full text-left font-mono text-[7px] uppercase tracking-wider text-primary-500 leading-tight truncate">
                        [X] [AVATAR: 1:1 — {{ strtoupper($nickname) }}]
                    </span>
                    <div class="relative flex-1 w-full flex items-center justify-center overflow-hidden rounded-lg my-0.5">
                        {{--
                            TODO: Jika mahasiswa belum memiliki foto formal ($student['formal_photo_url']),
                            sistem merender placeholder default '/assets/Placeholder.png'.
                        --}}
                        <img
                            src="{{ $avatarUrl }}"
                            alt="Foto {{ $nickname }}"
                            class="w-full h-full object-cover rounded-lg group-hover:scale-[1.03] transition-transform duration-200"
                            loading="lazy"
                            onerror="this.onerror=null; this.src='{{ asset($avatarPlaceholder) }}';"
                        />
                    </div>
                @endif
            </div>

            {{-- Student Identity Details --}}
            <div class="min-w-0 flex-1 pt-0.5">
                {{-- Nickname (20px - 22px Bold Deep Plum) --}}
                <h2 class="text-xl sm:text-[21px] font-bold text-primary-900 tracking-[-0.02em] leading-snug truncate">
                    {{ $nickname }}
                </h2>

                {{-- Full Name (13px - 14px Medium Deep Plum / 75%) --}}
                <p class="text-[13px] sm:text-sm font-medium text-primary-900/75 leading-snug line-clamp-2 mt-0.5">
                    {{ $locked ? 'Nama Lengkap Disamarkan' : $fullName }}
                </p>

                {{-- Monospace NIM Identifier --}}
                <p class="mt-2 inline-flex items-center px-2 py-0.5 rounded bg-white/80 border border-surface-border font-mono text-[11px] font-semibold text-primary-700 tracking-[0.04em]">
                    {{ $locked ? 'NIM // 21120126••••••' : 'NIM // ' . $nim }}
                </p>
            </div>

        </div>

        {{-- Origin City & Interest / Tech Tags --}}
        <div class="space-y-3 pt-3 border-t border-surface-border">
            {{-- Origin City with Monoline Pin Icon --}}
            <div class="flex items-center justify-between gap-2 text-xs text-primary-700">
                <span class="font-mono text-[10px] uppercase tracking-[0.05em] text-primary-500">
                    DOMISILI:
                </span>
                <span class="inline-flex items-center gap-1.5 font-semibold text-primary-900 truncate">
                    <svg class="size-3.5 shrink-0 stroke-[1.75] stroke-current text-secondary-700" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    <span>{{ $locked ? 'Kota Disamarkan' : $originCity }}</span>
                </span>
            </div>

            {{-- Tech / Exploration Tags --}}
            <div class="flex flex-wrap items-center gap-1.5">
                @if ($locked)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-surface-canvas border border-surface-border font-mono text-[10px] uppercase tracking-wider text-primary-500">
                        [DATA_PRIVAT_TERKUNCI]
                    </span>
                @else
                    @foreach (array_slice($hobbies, 0, 3) as $tag)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-white border border-surface-border font-mono text-[10px] font-medium uppercase tracking-wider text-primary-800">
                            #{{ $tag }}
                        </span>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- Bottom Action Row --}}
    <div class="mt-5 pt-3.5 border-t border-surface-border">
        @if ($locked)
            <a
                href="{{ $unlockUrl }}"
                class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg bg-highlight-500 text-primary-900 font-mono text-xs font-bold uppercase tracking-[0.04em] border border-surface-border hover:brightness-105 transition-all duration-180 focus-visible:outline-2 focus-visible:outline-secondary-500 focus-visible:outline-offset-2"
            >
                <svg class="size-3.5 shrink-0 stroke-[2] stroke-current text-primary-900" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
                <span>[Terkunci // Butuh Passcode]</span>
            </a>
        @else
            <a
                href="{{ $profileUrl }}"
                class="inline-flex items-center justify-between w-full px-4 py-2.5 rounded-lg bg-secondary-500 text-primary-900 font-semibold text-xs sm:text-sm border border-surface-border shadow-2xs hover:bg-secondary-700 hover:text-white active:scale-[0.99] transition-all duration-180 focus-visible:outline-2 focus-visible:outline-secondary-500 focus-visible:outline-offset-2"
            >
                <span>Lihat Profil</span>
                <span class="font-mono text-sm leading-none" aria-hidden="true">→</span>
            </a>
        @endif
    </div>
</article>
