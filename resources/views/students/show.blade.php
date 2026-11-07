<x-layouts.app :title="$student['nickname'] . ' — ' . $cohortName" activePage="unlock">

    {{-- Page Loading Transition Overlay --}}
    <div id="page-loading-overlay" class="fixed inset-0 z-[100] grid place-items-center bg-surface-canvas/95 px-6 backdrop-blur-xs" role="status" aria-live="polite" aria-label="Memuat profil mahasiswa">
        <div class="w-full max-w-md">
            <div class="mb-5 flex items-center justify-center gap-3 text-sm font-semibold text-primary-900">
                <span class="size-5 animate-spin rounded-full border-2 border-secondary-500/20 border-t-secondary-500" aria-hidden="true"></span>
                <span class="font-mono text-xs">// MEMUAT_DETAIL_MAHASISWA...</span>
            </div>
            <div class="grid grid-cols-3 gap-3" aria-hidden="true">
                <div class="aspect-square animate-pulse rounded-lg bg-surface-card shadow-xs"><div class="m-3 h-3 w-2/3 rounded bg-primary-900/10"></div><div class="mx-3 h-2 w-1/2 rounded bg-primary-900/5"></div></div>
                <div class="aspect-square animate-pulse rounded-lg bg-surface-card shadow-xs"><div class="m-3 h-3 w-2/3 rounded bg-primary-900/10"></div><div class="mx-3 h-2 w-1/2 rounded bg-primary-900/5"></div></div>
                <div class="aspect-square animate-pulse rounded-lg bg-surface-card shadow-xs"><div class="m-3 h-3 w-2/3 rounded bg-primary-900/10"></div><div class="mx-3 h-2 w-1/2 rounded bg-primary-900/5"></div></div>
            </div>
        </div>
    </div>
    <noscript><style>#page-loading-overlay { display: none !important; }</style></noscript>

    {{-- Main Content: Screen 4 Student Detail Bento Profile (Issue #15) --}}
    <main id="main-content" tabindex="-1" aria-busy="true" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
        <x-student-profile-bento
            :student="$student"
            :prevStudent="$prevStudent"
            :nextStudent="$nextStudent"
            :currentPosition="$currentPosition"
            :totalInClass="$totalInClass"
        />
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
