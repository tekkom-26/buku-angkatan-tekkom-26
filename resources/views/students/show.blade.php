<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $student['nickname'] }} — Ascent Teknik Komputer 2026</title>
        @fonts
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-brand-mist text-brand-deep antialiased">

        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[110] focus:rounded-lg focus:bg-white focus:px-4 focus:py-3 focus:font-semibold focus:text-brand-deep focus:shadow-lg">
            Lewati ke konten utama
        </a>

        <div id="page-loading-overlay" class="fixed inset-0 z-[100] grid place-items-center bg-brand-mist/95 px-6" role="status" aria-live="polite" aria-label="Memuat profil mahasiswa">
            <div class="w-full max-w-md">
                <div class="mb-5 flex items-center justify-center gap-3 text-sm font-semibold text-brand-deep">
                    <span class="size-5 animate-spin rounded-full border-2 border-brand-teal/20 border-t-brand-teal" aria-hidden="true"></span>
                    <span>Memuat profil mahasiswa...</span>
                </div>
                <div class="grid grid-cols-3 gap-3" aria-hidden="true">
                    <div class="aspect-square animate-pulse rounded-lg bg-white shadow-sm"><div class="m-3 h-3 w-2/3 rounded bg-brand-deep/10"></div><div class="mx-3 h-2 w-1/2 rounded bg-brand-deep/5"></div></div>
                    <div class="aspect-square animate-pulse rounded-lg bg-white shadow-sm"><div class="m-3 h-3 w-2/3 rounded bg-brand-deep/10"></div><div class="mx-3 h-2 w-1/2 rounded bg-brand-deep/5"></div></div>
                    <div class="aspect-square animate-pulse rounded-lg bg-white shadow-sm"><div class="m-3 h-3 w-2/3 rounded bg-brand-deep/10"></div><div class="mx-3 h-2 w-1/2 rounded bg-brand-deep/5"></div></div>
                </div>
            </div>
        </div>
        <noscript><style>#page-loading-overlay { display: none !important; }</style></noscript>

        {{-- Navbar --}}
        <x-navbar activePage="unlock" />


        <main id="main-content" tabindex="-1" aria-busy="true" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">


            <div class="mb-6">
                <a href="/directory"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-deep/50 hover:text-brand-teal transition-colors group"
                >
                    <flux:icon icon="arrow-left" class="size-4 group-hover:-translate-x-0.5 transition-transform" />
                    Kembali ke Direktori
                </a>
            </div>


            <section class="mb-10">
                <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">


                    <div class="lg:w-[420px] shrink-0">
                        <div class="relative">

                            {{-- Main photo (Formal) — larger, offset right --}}
                            <div class="relative z-10 rounded-2xl overflow-hidden shadow-xl border border-white/60 bg-white p-1.5">
                                <div class="rounded-xl overflow-hidden aspect-[3/4] bg-brand-deep/5">
                                    <img
                                        src="{{ $student['formal_photo_url'] }}"
                                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($student['nickname']) }}&background=176B70&color=fff&size=800'"
                                        alt="Foto resmi {{ $student['name'] }}"
                                        class="w-full h-full object-cover"
                                        fetchpriority="high"
                                    />
                                </div>
                                {{-- Label badge --}}
                                <div class="absolute top-3 left-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider bg-white/90 text-brand-deep backdrop-blur-sm shadow-sm border border-brand-deep/8">
                                        <flux:icon icon="identification" class="size-3 me-1 text-brand-teal" />
                                        Foto Resmi
                                    </span>
                                </div>
                            </div>

                            {{-- Secondary photo (Casual) — smaller, overlapping bottom-left --}}
                            <div class="absolute -bottom-4 -left-3 sm:-left-5 z-20 w-[160px] sm:w-[190px] rounded-xl overflow-hidden shadow-lg border-3 border-white bg-white p-1 rotate-[-3deg] hover:rotate-0 transition-transform duration-300">
                                <div class="rounded-lg overflow-hidden aspect-square bg-brand-teal/5">
                                    <img
                                        src="{{ $student['casual_photo_url'] }}"
                                        alt="Foto casual {{ $student['nickname'] }}"
                                        class="w-full h-full object-cover"
                                        loading="lazy"
                                    />
                                </div>
                                <div class="absolute bottom-2 left-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider bg-black/50 text-white backdrop-blur-sm">
                                        <flux:icon icon="sparkles" class="size-2.5 me-1 text-brand-mint" />
                                        Casual
                                    </span>
                                </div>
                            </div>

                            {{-- Decorative accent blob behind photos --}}
                            <div class="absolute -top-4 -right-4 w-32 h-32 rounded-full bg-brand-cyan/15 blur-2xl -z-10"></div>
                            <div class="absolute -bottom-6 -right-6 w-24 h-24 rounded-full bg-brand-mint/20 blur-2xl -z-10"></div>
                        </div>
                    </div>


                    <div class="flex-1 flex flex-col justify-center min-w-0">

                        {{-- Class badge + NIM row --}}
                        <div class="flex flex-wrap items-center gap-2.5 mb-4">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold uppercase tracking-wider bg-brand-cyan/12 text-brand-deep border border-brand-cyan/25">
                                <flux:icon icon="academic-cap" class="size-3.5 me-1.5 text-brand-cyan" />
                                Kelas {{ $student['class_name'] }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-mono text-brand-deep/45 bg-brand-deep/4 border border-brand-deep/8">
                                NIM: {{ $student['nim'] }}
                            </span>
                        </div>

                        {{-- Name --}}
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-brand-deep tracking-tight leading-[1.1] mb-1">
                            {{ $student['nickname'] }}
                        </h1>
                        <p class="text-base sm:text-lg text-brand-deep/55 mb-6 font-medium">
                            {{ $student['name'] }}
                        </p>

                        {{-- Origin city quick info --}}
                        <div class="flex items-center gap-2 text-sm text-brand-deep/50 mb-7">
                            <flux:icon icon="map-pin" class="size-4 text-brand-teal" />
                            <span>Asal {{ $student['origin_city'] }}</span>
                            <span class="text-brand-deep/15">·</span>
                            <flux:icon icon="heart" class="size-4 text-brand-teal ms-1" />
                            <span>{{ $student['religion'] }}</span>
                        </div>

                        {{-- Bio Quote — styled as a beautiful blockquote --}}
                        <div class="relative rounded-2xl bg-gradient-to-br from-brand-teal/5 via-brand-cyan/5 to-brand-mint/10 border border-brand-teal/12 p-6 sm:p-8">
                            {{-- Decorative quote mark --}}
                            <svg class="absolute top-4 left-4 size-10 text-brand-teal/10" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151C7.546 6.068 5.983 8.789 5.983 11h4v10H0z"/>
                            </svg>
                            <blockquote class="relative z-10">
                                <p class="text-base sm:text-lg text-brand-deep/75 leading-relaxed italic pl-6 font-light">
                                    "{{ $student['bio_quote'] }}"
                                </p>
                                <cite class="not-italic block mt-3 pl-6 text-xs font-semibold text-brand-teal/60 uppercase tracking-wider">
                                    — {{ $student['nickname'] }}'s Life Motto
                                </cite>
                            </blockquote>
                        </div>
                    </div>

                </div>
            </section>


            <div class="flex items-center gap-3 mb-8">
                <div class="h-px flex-1 bg-gradient-to-r from-transparent via-brand-teal/20 to-transparent"></div>
                <flux:icon icon="user-group" class="size-4 text-brand-teal/30" />
                <div class="h-px flex-1 bg-gradient-to-r from-transparent via-brand-teal/20 to-transparent"></div>
            </div>


            <section class="mb-10">
                <h2 class="text-sm font-bold uppercase tracking-widest text-brand-deep/35 mb-5 flex items-center gap-2">
                    <flux:icon icon="clipboard-document" class="size-4" />
                    Biodata Lengkap
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    {{-- Card: TTL --}}
                    <div class="rounded-xl border border-brand-teal/12 bg-white shadow-sm p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-2.5 mb-3">
                            <div class="w-9 h-9 rounded-lg bg-brand-cyan/10 flex items-center justify-center shrink-0">
                                <flux:icon icon="cake" class="size-4.5 text-brand-cyan" />
                            </div>
                            <h3 class="text-xs font-semibold text-brand-deep/40 uppercase tracking-wider">Tempat, Tanggal Lahir</h3>
                        </div>
                        <p class="text-sm font-semibold text-brand-deep">{{ $student['birth_place'] }}</p>
                        <p class="text-xs text-brand-deep/55 mt-0.5">{{ $student['birth_date'] }}</p>
                    </div>

                    {{-- Card: Kota Asal --}}
                    <div class="rounded-xl border border-brand-teal/12 bg-white shadow-sm p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-2.5 mb-3">
                            <div class="w-9 h-9 rounded-lg bg-brand-mint/25 flex items-center justify-center shrink-0">
                                <flux:icon icon="map-pin" class="size-4.5 text-brand-teal" />
                            </div>
                            <h3 class="text-xs font-semibold text-brand-deep/40 uppercase tracking-wider">Kota Asal</h3>
                        </div>
                        <p class="text-sm font-semibold text-brand-deep">{{ $student['origin_city'] }}</p>
                    </div>

                    {{-- Card: Agama --}}
                    <div class="rounded-xl border border-brand-teal/12 bg-white shadow-sm p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-2.5 mb-3">
                            <div class="w-9 h-9 rounded-lg bg-brand-deep/5 flex items-center justify-center shrink-0">
                                <flux:icon icon="heart" class="size-4.5 text-brand-deep/50" />
                            </div>
                            <h3 class="text-xs font-semibold text-brand-deep/40 uppercase tracking-wider">Agama</h3>
                        </div>
                        <p class="text-sm font-semibold text-brand-deep">{{ $student['religion'] }}</p>
                    </div>

                    {{-- Card: Tempat Makan Favorit --}}
                    <div class="rounded-xl border border-brand-teal/12 bg-white shadow-sm p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-2.5 mb-3">
                            <div class="w-9 h-9 rounded-lg bg-orange-50 flex items-center justify-center shrink-0">
                                <flux:icon icon="face-smile" class="size-4.5 text-orange-400" />
                            </div>
                            <h3 class="text-xs font-semibold text-brand-deep/40 uppercase tracking-wider">Makanan Favorit</h3>
                        </div>
                        <p class="text-sm font-semibold text-brand-deep leading-snug">{{ $student['favorite_food_place'] }}</p>
                    </div>

                </div>
            </section>


            <section class="mb-10">
                <h2 class="text-sm font-bold uppercase tracking-widest text-brand-deep/35 mb-4 flex items-center gap-2">
                    <flux:icon icon="sparkles" class="size-4" />
                    Hobi & Minat
                </h2>

                <div class="flex flex-wrap gap-2.5">
                    @foreach ($student['hobbies'] as $hobby)
                        <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium bg-white border border-brand-teal/12 text-brand-deep shadow-sm hover:shadow-md hover:border-brand-teal/25 hover:-translate-y-0.5 transition-all cursor-default">
                            <flux:icon icon="heart" class="size-3.5 me-1.5 text-brand-mint/60" />
                            {{ ucfirst($hobby) }}
                        </span>
                    @endforeach
                </div>
            </section>


            <section class="mb-10">
                <h2 class="text-sm font-bold uppercase tracking-widest text-brand-deep/35 mb-5 flex items-center gap-2">
                    <flux:icon icon="musical-note" class="size-4" />
                    Sosial & Musik Favorit
                </h2>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">


                    {{-- Instagram Card --}}
                    <div class="rounded-2xl border border-brand-teal/12 bg-white shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center gap-4">
                                {{-- Instagram avatar circle --}}
                                <div class="w-14 h-14 rounded-full bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 p-[2px]">
                                    <div class="w-full h-full rounded-full bg-white p-[2px]">
                                        <img
                                            src="{{ $student['formal_photo_url'] }}"
                                            alt="@{{ $student['instagram_username'] }}"
                                            class="w-full h-full rounded-full object-cover"
                                            loading="lazy"
                                        />
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-brand-deep/40 uppercase tracking-wider">Instagram</p>
                                    <p class="text-lg font-bold text-brand-deep truncate">@ {{ $student['instagram_username'] }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 pb-2">
                            <a href="{{ $student['instagram_url'] }}" target="_blank" rel="noopener noreferrer"
                               class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-gradient-to-r from-yellow-400/10 via-pink-500/10 to-purple-600/10 border border-pink-200/40 text-sm font-semibold text-brand-deep hover:from-yellow-400/18 hover:via-pink-500/18 hover:to-purple-600/18 transition-all group"
                            >
                                <svg class="size-5 text-pink-500" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                </svg>
                                Kunjungi Profil Instagram
                                <flux:icon icon="arrow-right" class="size-3.5 opacity-0 group-hover:opacity-100 -translate-x-1 group-hover:translate-x-0 transition-all" />
                            </a>
                        </div>
                    </div>


                    {{-- Spotify Embed Card --}}
                    <div class="rounded-2xl border border-brand-teal/12 bg-white shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-6 pb-4">
                            <div class="flex items-center gap-3 mb-1">
                                <div class="w-10 h-10 rounded-xl bg-green-500 flex items-center justify-center">
                                    <svg class="size-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 0C5.4 0 0 5.4 0 12s5.4 12 12 12 12-5.4 12-12S18.66 0 12 0zm5.521 17.34c-.24.359-.66.48-1.021.24-2.82-1.74-6.36-2.101-10.561-1.141-.418.122-.779-.179-.899-.539-.12-.421.18-.78.54-.9 4.56-1.021 8.52-.6 11.64 1.32.42.18.479.659.301 1.02zm1.44-3.3c-.301.42-.841.6-1.262.3-3.239-1.98-8.159-2.58-11.939-1.38-.479.12-1.02-.12-1.14-.6-.12-.48.12-1.021.6-1.141C9.6 9.9 15 10.561 18.72 12.84c.36.181.54.78.241 1.2zm.12-3.36C15.24 8.4 8.82 8.16 5.16 9.301c-.6.179-1.2-.181-1.38-.721-.18-.601.18-1.2.72-1.381 4.26-1.28 11.28-1.03 15.721 1.36.541.3.719 1.02.419 1.56-.299.421-1.02.599-1.559.3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-brand-deep/40 uppercase tracking-wider">Lagu Favorit di Spotify</p>
                                    <p class="text-sm font-semibold text-brand-deep">Dengarkan sekarang 🎧</p>
                                </div>
                            </div>
                        </div>
                        <div class="px-6 pb-6">
                            @if ($student['spotify_track_id'])
                                <div class="relative min-h-[352px] overflow-hidden rounded-xl bg-brand-deep/3">
                                    <div id="spotify-loading" class="absolute inset-0 z-10 grid content-center gap-4 bg-white p-5" aria-hidden="true">
                                        <div class="h-4 w-2/3 animate-pulse rounded bg-brand-deep/10"></div>
                                        <div class="h-3 w-1/2 animate-pulse rounded bg-brand-deep/5"></div>
                                        <div class="h-10 w-full animate-pulse rounded-lg bg-brand-deep/5"></div>
                                    </div>
                                    <p id="spotify-status" class="sr-only" role="status" aria-live="polite">Memuat pemutar Spotify.</p>
                                    <iframe id="spotify-embed" data-testid="embed-iframe" title="Lagu favorit {{ $student['nickname'] }} di Spotify" style="border-radius:12px" src="https://open.spotify.com/embed/track/{{ $student['spotify_track_id'] }}?utm_source=generator&si=353177db515e4495" width="100%" height="352" frameborder="0" allowfullscreen="" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>
                                </div>
                            @else
                                <p class="rounded-xl bg-brand-deep/3 p-5 text-sm text-brand-deep/55" role="status">Lagu Spotify belum ditambahkan.</p>
                            @endif
                        </div>
                    </div>

                </div>
            </section>


            <div class="flex items-center justify-between pt-6 border-t border-brand-teal/10">
                @if($prevStudent)
                    <a href="/students/{{ $prevStudent['nim'] }}"
                       class="group inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-brand-deep/55 hover:text-brand-deep bg-white border border-brand-teal/10 hover:border-brand-teal/25 hover:shadow-sm transition-all"
                       aria-label="Mahasiswa sebelumnya: {{ $prevStudent['nickname'] }}"
                    >
                        <flux:icon icon="arrow-left" class="size-4 group-hover:-translate-x-0.5 transition-transform" aria-hidden="true" />
                        <span class="hidden sm:inline">Sebelumnya:</span>
                        <span class="font-semibold">{{ $prevStudent['nickname'] }}</span>
                    </a>
                @else
                    <div></div>
                @endif

                <span class="text-xs text-brand-deep/25 font-mono">
                    {{ $currentPosition }} / {{ $totalInClass }} (Kelas {{ $student['class_name'] }})
                </span>

                @if($nextStudent)
                    <a href="/students/{{ $nextStudent['nim'] }}"
                       class="group inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-brand-deep/55 hover:text-brand-deep bg-white border border-brand-teal/10 hover:border-brand-teal/25 hover:shadow-sm transition-all"
                              aria-label="Mahasiswa berikutnya: {{ $nextStudent['nickname'] }}"
                    >
                        <span class="font-semibold">{{ $nextStudent['nickname'] }}</span>
                        <span class="hidden sm:inline">:Selanjutnya</span>
                        <flux:icon icon="arrow-right" class="size-4 group-hover:translate-x-0.5 transition-transform" />
                    </a>
                @else
                    <div></div>
                @endif
            </div>


        </main>


        {{-- Footer --}}
        <x-footer activePage="unlock" />

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
                        document.getElementById('spotify-loading').hidden = true;
                        document.getElementById('spotify-status').textContent = 'Pemutar Spotify siap.';
                    });
                }
            })();
        </script>

    </body>
</html>
