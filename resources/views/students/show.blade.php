<x-layouts.app :title="$student['nickname'] . ' — ' . $cohortName" activePage="directory">

    {{-- Page Loading Transition Overlay (COMETS 2026 Dark Theme) --}}
    <div
        id="page-loading-overlay"
        class="fixed inset-0 z-[100] grid place-items-center bg-canvas-base/95 px-6 backdrop-blur-md"
        role="status"
        aria-live="polite"
        aria-label="Memuat profil mahasiswa"
    >
        <div class="w-full max-w-md rounded-xl border border-brand-gold/30 bg-surface-card/90 p-6 glow-gold-subtle">
            <div class="mb-5 flex items-center justify-center gap-3 text-sm font-semibold text-text-primary">
                <span class="size-5 animate-spin rounded-full border-2 border-brand-gold/20 border-t-brand-gold" aria-hidden="true"></span>
                <span class="font-mono text-xs text-brand-gold">// MEMUAT_DETAIL_MAHASISWA...</span>
            </div>
            <div class="grid grid-cols-3 gap-3" aria-hidden="true">
                <div class="aspect-square animate-pulse rounded-lg border border-white/10 bg-surface-elevated">
                    <div class="m-3 h-3 w-2/3 rounded bg-white/15"></div>
                    <div class="mx-3 h-2 w-1/2 rounded bg-white/10"></div>
                </div>
                <div class="aspect-square animate-pulse rounded-lg border border-white/10 bg-surface-elevated">
                    <div class="m-3 h-3 w-2/3 rounded bg-white/15"></div>
                    <div class="mx-3 h-2 w-1/2 rounded bg-white/10"></div>
                </div>
                <div class="aspect-square animate-pulse rounded-lg border border-white/10 bg-surface-elevated">
                    <div class="m-3 h-3 w-2/3 rounded bg-white/15"></div>
                    <div class="mx-3 h-2 w-1/2 rounded bg-white/10"></div>
                </div>
            </div>
        </div>
    </div>
    <noscript><style>#page-loading-overlay { display: none !important; }</style></noscript>

    {{-- Main Content: Phase 4 Student Profile Detail Split-Screen Bento (Issue #58) --}}
    <main
        id="main-content"
        tabindex="-1"
        aria-busy="true"
        class="relative grow w-full bg-ambient-hero py-6 sm:py-10 lg:py-14 overflow-hidden"
    >
        {{-- Decorative Background Circuit Traces --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden select-none" aria-hidden="true">
            <svg class="hidden lg:block absolute right-0 top-6 w-80 h-80 text-brand-gold/20" viewBox="0 0 320 320" fill="none">
                <path d="M320 60 H210 L175 95 H105" stroke="currentColor" stroke-width="1.25" />
                <path d="M320 140 H240 L205 175 H145" stroke="currentColor" stroke-width="1" stroke-dasharray="4 4" class="text-brand-red-hover/40" />
                <circle cx="105" cy="95" r="3.5" class="fill-brand-gold" />
                <circle cx="145" cy="175" r="3.5" class="fill-brand-red-hover" />
            </svg>
            <svg class="hidden lg:block absolute left-0 bottom-10 w-72 h-72 text-brand-red-hover/25" viewBox="0 0 320 320" fill="none">
                <path d="M0 100 H110 L145 135 H215" stroke="currentColor" stroke-width="1.25" />
                <circle cx="215" cy="135" r="3.5" class="fill-brand-gold" />
            </svg>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-student-profile-bento
                :student="$student"
                :prevStudent="$prevStudent"
                :nextStudent="$nextStudent"
                :currentPosition="$currentPosition"
                :totalInClass="$totalInClass"
            />
        </div>
    </main>

    @push('scripts')
        <script>
            (() => {
                const overlay = document.getElementById('page-loading-overlay');
                const main = document.getElementById('main-content');
                const spotifyEmbed = document.getElementById('spotify-embed');

                const setPageLoading = (isLoading) => {
                    overlay.hidden = !isLoading;
                    main.setAttribute('aria-busy', String(isLoading));
                };

                const hidePageLoading = () => setPageLoading(false);

                if (document.readyState === 'complete') {
                    hidePageLoading();
                } else {
                    window.addEventListener('load', hidePageLoading, { once: true });
                }

                window.addEventListener('pageshow', (event) => {
                    if (event.persisted) {
                        hidePageLoading();
                    }
                });

                document.addEventListener('click', (event) => {
                    const link = event.target.closest('a[href]');

                    if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.target === '_blank' || link.hasAttribute('download')) {
                        return;
                    }

                    const destination = new URL(link.href, window.location.href);
                    const isSamePageAnchor = destination.origin === window.location.origin
                        && destination.pathname === window.location.pathname
                        && destination.search === window.location.search
                        && destination.hash;

                    if (destination.origin === window.location.origin && !isSamePageAnchor) {
                        setPageLoading(true);
                    }
                });

                if (spotifyEmbed) {
                    spotifyEmbed.addEventListener('load', () => {
                        const loader = document.getElementById('spotify-loading');
                        const status = document.getElementById('spotify-status');
                        if (loader) loader.hidden = true;
                        if (status) status.textContent = 'Pemutar Spotify siap.';
                    });
                }
            })();
        </script>
    @endpush

</x-layouts.app>
