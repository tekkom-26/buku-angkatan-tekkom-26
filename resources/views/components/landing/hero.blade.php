@props([
    'title' => 'ANGKATAN // TEKKOM 2026',
    'motto' => 'LOREM IPSUM DOLOR!!!',
    'narrative' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.',
    'directoryUrl' => '/directory',
    'identityUrl' => '/identity',
    'mascotPlaceholder' => '/assets/Placeholder.png',
])

{{-- ==========================================================================
     SECTION 1: HERO ASYMMETRIC BENTO GRID (65% Left / 35% Right)
     Mengacu pada GitHub Issue #18, DESIGN.md, dan prompts/screen-1-landing.md
     Figma Reference: Node 24:505 (Hero Container) & Node 24:553 (Mascot Chibi)
     ========================================================================== --}}
<section id="hero-section" aria-label="Hero Section Ascent" class="w-full bg-surface-canvas py-8 sm:py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Bento Grid Container (Asymmetric 65% Left / 35% Right) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

            {{-- ── A. LEFT HERO CARD (65% Width / lg:col-span-8) ── --}}
            <div class="lg:col-span-8 bg-surface-card border border-surface-border rounded-2xl p-6 sm:p-8 lg:p-10 flex flex-col justify-between shadow-xs relative overflow-hidden transition-all duration-200">

                <div>
                    {{-- 1. Monospace Technical Index Tag --}}
                    <div class="flex items-center gap-2 mb-4">
                        <span class="inline-block w-2 h-2 rounded-full bg-secondary-500" aria-hidden="true"></span>
                        <span class="font-mono text-[11px] sm:text-xs font-bold uppercase tracking-wider text-primary-900/60">
                            // 01_HERO // BATCH_XXVI
                        </span>
                    </div>

                    {{-- 2. Hero Headline Display --}}
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-[52px] font-extrabold text-primary-900 tracking-[-0.025em] leading-[1.08] mb-5">
                        {{ $title }}
                    </h1>

                    {{-- 3. Official Motto Callout --}}
                    <div class="border-l-2 border-l-secondary-500 pl-4 py-2 my-5 bg-white/45 rounded-r-lg">
                        <blockquote class="text-base sm:text-lg font-semibold italic text-primary-900 tracking-tight">
                            "{{ $motto }}"
                        </blockquote>
                    </div>

                    {{-- 4. Narrative Paragraph (Unfinalized Philosophy Copy) --}}
                    <p class="text-sm sm:text-[15px] leading-relaxed text-primary-900/85 max-w-2xl mt-4 mb-8">
                        {{ $narrative }}
                    </p>
                </div>

                {{-- 5. Action Row (Primary & Secondary Buttons) --}}
                <div class="flex flex-wrap items-center gap-3 sm:gap-4 pt-2">
                    {{-- Primary CTA Button --}}
                    <a
                        href="{{ $directoryUrl }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-secondary-500 text-primary-900 font-semibold text-sm sm:text-base border border-surface-border shadow-xs hover:brightness-105 active:scale-[0.98] transition-all duration-180 focus-visible:outline-2 focus-visible:outline-secondary-500 focus-visible:outline-offset-2"
                    >
                        <span>Jelajahi Direktori Angkatan</span>
                        <svg class="size-4 shrink-0 stroke-[2.2] stroke-current" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>

                    {{-- Secondary Ghost Button --}}
                    <a
                        href="{{ $identityUrl }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg border border-surface-border bg-white/50 text-primary-900 font-semibold text-sm sm:text-base hover:bg-white hover:border-primary-900/35 active:scale-[0.98] transition-all duration-180 focus-visible:outline-2 focus-visible:outline-secondary-500 focus-visible:outline-offset-2"
                    >
                        <span>Baca Filosofi Lambang</span>
                        <svg class="size-4 shrink-0 stroke-[2] stroke-current" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                    </a>
                </div>

            </div>

            {{-- ── B. RIGHT HERO CARD (35% Width / lg:col-span-4) ── --}}
            <div class="lg:col-span-4 bg-white border border-surface-border rounded-2xl p-6 sm:p-8 flex flex-col justify-between gap-6 shadow-xs relative overflow-hidden transition-all duration-200">

                {{-- 1. Top Authorization Badge (Butter Cream #d4d86a) --}}
                <div class="flex items-center justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-highlight-500 text-highlight-900 font-mono text-[11px] font-bold tracking-wider shadow-xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-highlight-900 animate-pulse" aria-hidden="true"></span>
                        [STATUS: ANGKATAN AKTIF // 2026]
                    </span>
                </div>

                {{-- 2. Center Graphic (Mascot Chibi Gajah Tekkom Placeholder Image) --}}
                <div class="w-full flex items-center justify-center my-auto py-2">
                    <div class="relative w-full max-w-[280px] sm:max-w-[312px] aspect-square rounded-xl overflow-hidden border border-surface-border bg-surface-canvas flex items-center justify-center p-3 shadow-xs">
                        <img
                            src="{{ asset($mascotPlaceholder) }}"
                            alt="[ASSET: MASCOT_CHIBI - GAJAH TEKKOM]"
                            class="w-full h-full object-contain"
                            loading="lazy"
                        />
                    </div>
                </div>

                {{-- 3. Bottom Monospace Cohort Specifications --}}
                <div class="border-t border-surface-border pt-5 space-y-2.5">
                    <div class="flex items-start justify-between gap-2 font-mono text-[11px] sm:text-xs">
                        <span class="text-primary-500 uppercase tracking-wider">DEPT:</span>
                        <span class="font-semibold text-primary-900 text-right">TEKNIK KOMPUTER</span>
                    </div>
                    <div class="flex items-start justify-between gap-2 font-mono text-[11px] sm:text-xs">
                        <span class="text-primary-500 uppercase tracking-wider">FACULTY:</span>
                        <span class="font-semibold text-primary-900 text-right">FT UNIVERSITAS DIPONEGORO</span>
                    </div>
                    <div class="flex items-start justify-between gap-2 font-mono text-[11px] sm:text-xs">
                        <span class="text-primary-500 uppercase tracking-wider">STUDENTS_COUNT:</span>
                        <span class="font-semibold text-secondary-700 text-right">205 STUDENTS</span>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>
