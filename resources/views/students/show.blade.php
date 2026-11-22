<x-layouts.app :title="$student['nickname'] . ' — ' . $cohortName" activePage="directory">

    {{-- Page Loading Transition Overlay (COMETS 2026 Charcoal & Tech Gold Theme) --}}
    <div id="page-loading-overlay" class="fixed inset-0 z-[100] grid place-items-center bg-canvas-base/95 px-6 backdrop-blur-xs" role="status" aria-live="polite" aria-label="Memuat profil mahasiswa">
        <div class="w-full max-w-md">
            <div class="mb-5 flex items-center justify-center gap-3 text-sm font-semibold text-text-primary">
                <span class="size-5 animate-spin rounded-full border-2 border-brand-gold/20 border-t-brand-gold" aria-hidden="true"></span>
                <span class="font-mono text-xs text-brand-gold">// MEMUAT_DETAIL_MAHASISWA...</span>
            </div>
            <div class="grid grid-cols-3 gap-3" aria-hidden="true">
                <div class="aspect-square animate-pulse rounded-lg bg-surface-card border border-white/10"><div class="m-3 h-3 w-2/3 rounded bg-brand-gold/15"></div><div class="mx-3 h-2 w-1/2 rounded bg-white/10"></div></div>
                <div class="aspect-square animate-pulse rounded-lg bg-surface-card border border-white/10"><div class="m-3 h-3 w-2/3 rounded bg-brand-gold/15"></div><div class="mx-3 h-2 w-1/2 rounded bg-white/10"></div></div>
                <div class="aspect-square animate-pulse rounded-lg bg-surface-card border border-white/10"><div class="m-3 h-3 w-2/3 rounded bg-brand-gold/15"></div><div class="mx-3 h-2 w-1/2 rounded bg-white/10"></div></div>
            </div>
        </div>
    </div>
    <noscript><style>#page-loading-overlay { display: none !important; }</style></noscript>

    {{-- Main Content: Student Detail Split-Screen Bento Profile (PRD §3.4) --}}
    <main id="main-content" tabindex="-1" aria-busy="true" class="relative grow w-full bg-ambient-hero py-6 sm:py-10 lg:py-12 overflow-hidden">
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
