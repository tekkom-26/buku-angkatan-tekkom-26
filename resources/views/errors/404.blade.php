<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Halaman Tidak Ditemukan — Tekkom 26</title>
        @fonts
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="min-h-screen overflow-x-hidden bg-surface-canvas text-primary-900 antialiased">
        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-10 focus:rounded-lg focus:bg-surface-canvas focus:px-4 focus:py-3 focus:font-semibold focus:text-primary-900 focus:shadow-lg">
            Lewati ke konten utama
        </a>

        <header class="border-b border-surface-border bg-surface-canvas">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-5 sm:px-6 lg:px-8">
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-3 rounded-lg font-semibold text-primary-900">
                    <span class="grid size-10 place-items-center rounded-lg border border-surface-border bg-surface-card font-mono text-sm text-primary-900" aria-hidden="true">[X]</span>
                    <span>TEKKOM 2026 <span class="block font-mono text-[10px] uppercase tracking-[0.18em] text-primary-500">Ascent Digital</span></span>
                </a>
                <span class="hidden font-mono text-[10px] uppercase tracking-[0.2em] text-primary-500 sm:block">SYSTEM / ERROR HANDLER</span>
            </div>
        </header>

        <main id="main-content" tabindex="-1" class="mx-auto max-w-7xl px-4 py-10 sm:px-6 md:py-16 lg:px-8">
            <div class="mb-8 grid grid-cols-2 border-y border-surface-border sm:grid-cols-4">
                <div class="border-r border-surface-border px-4 py-4 sm:px-5">
                    <p class="font-mono text-[9px] uppercase tracking-[0.18em] text-primary-500">STATUS</p>
                    <p class="mt-2 text-xs font-semibold text-primary-900">404 / NOT FOUND</p>
                </div>
                <div class="border-r border-surface-border px-4 py-4 sm:px-5">
                    <p class="font-mono text-[9px] uppercase tracking-[0.18em] text-primary-500">MEMORY</p>
                    <p class="mt-2 text-xs font-semibold text-primary-900">UNALLOCATED</p>
                </div>
                <div class="border-r border-surface-border px-4 py-4 sm:px-5">
                    <p class="font-mono text-[9px] uppercase tracking-[0.18em] text-primary-500">NODE</p>
                    <p class="mt-2 text-xs font-semibold text-primary-900">SCREEN_07</p>
                </div>
                <div class="px-4 py-4 sm:px-5">
                    <p class="font-mono text-[9px] uppercase tracking-[0.18em] text-primary-500">RECOVERY</p>
                    <p class="mt-2 text-xs font-semibold text-secondary-700">AVAILABLE</p>
                </div>
            </div>

            <div class="grid overflow-hidden rounded-2xl border border-surface-border bg-surface-card lg:grid-cols-[1.5fr_1fr]">
                <section class="border-b border-surface-border p-7 sm:p-10 lg:border-b-0 lg:border-r">
                    <p class="font-mono text-[10px] uppercase tracking-[0.22em] text-secondary-700">// 07_ERR_404 // MEMORY_SEGMENTATION_FAULT</p>
                    <h1 class="mt-5 max-w-2xl text-6xl font-black leading-[0.82] tracking-[-0.09em] text-primary-900 sm:text-8xl">
                        404<span class="text-secondary-500">.</span>
                    </h1>
                    <h2 class="mt-8 max-w-xl text-2xl font-bold leading-tight tracking-[-0.04em] text-primary-900 sm:text-3xl">
                        Halaman ini tidak ditemukan.
                    </h2>
                    <p class="mt-4 max-w-xl text-sm leading-7 text-primary-700 sm:text-base">
                        Alamat yang kamu cari belum dialokasikan di sistem Ascent. Gunakan jalur pemulihan di bawah untuk kembali ke jaringan utama.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                        <a href="{{ route('landing') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-primary-900 px-5 text-sm font-semibold text-white transition hover:bg-secondary-500 hover:text-primary-900">
                            ← Kembali ke Beranda Utama
                        </a>
                        <a href="{{ route('directory.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-surface-border bg-surface-canvas px-5 text-sm font-semibold text-primary-900 transition hover:bg-surface-subtle">
                            Jelajahi Direktori Mahasiswa →
                        </a>
                    </div>

                    <form method="GET" action="{{ route('directory.index') }}" role="search" class="mt-10 flex flex-col gap-3 border-t border-surface-border pt-6 sm:flex-row">
                        <label for="student-search" class="sr-only">Cari nama atau NIM mahasiswa</label>
                        <input id="student-search" name="search" type="search" placeholder="Cari nama atau NIM mahasiswa" class="min-h-11 min-w-0 flex-1 rounded-lg border border-surface-border bg-surface-canvas px-4 text-sm text-primary-900 placeholder:text-primary-500 focus:border-secondary-500 focus:outline-none">
                        <button type="submit" class="min-h-11 rounded-lg bg-secondary-500 px-5 text-sm font-semibold text-primary-900 transition hover:bg-highlight-500">
                            Cari di Direktori
                        </button>
                    </form>
                </section>

                <aside class="flex flex-col justify-between bg-surface-canvas p-7 sm:p-10">
                    <div>
                        <div class="flex items-center justify-between border-b border-surface-border pb-5">
                            <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-primary-500">ERROR_DIAGNOSTIC</p>
                            <span class="rounded-full bg-highlight-500 px-3 py-1 font-mono text-[10px] font-medium text-primary-900">TRACE_404</span>
                        </div>

                        <div class="mx-auto mt-8 flex aspect-square w-full max-w-[180px] flex-col items-center justify-between rounded-xl border border-surface-border bg-surface-card p-3">
                            <span class="w-full text-left font-mono text-[7px] uppercase tracking-wider text-primary-500">[X] [ILLUSTRATION: 404_MASCOT_LOST_1:1]</span>
                            <div class="relative flex size-full items-center justify-center overflow-hidden rounded-lg border border-dashed border-surface-border p-2">
                                {{--
                                    TODO: Ganti '/assets/Placeholder.png' dengan path gambar PNG/JPG maskot 404 saat aset final tersedia:
                                    contoh: asset('/assets/mascot/404-lost.png')
                                --}}
                                <img
                                    src="{{ asset('/assets/Placeholder.png') }}"
                                    alt="Maskot Gajah 404 — Halaman Tidak Ditemukan"
                                    class="h-full w-full object-contain select-none"
                                    loading="lazy"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 space-y-3 font-mono text-[10px] leading-5 text-primary-700">
                        <p class="text-primary-500">[ERR_STATUS // 0x0194_NOT_FOUND]</p>
                        <p><span class="text-secondary-700">TRACE</span> / route_not_found</p>
                        <p><span class="text-secondary-700">CAUSE</span> / unallocated_address</p>
                        <p><span class="text-secondary-700">ACTION</span> / reroute_to_home</p>
                        <p class="border-t border-surface-border pt-3 text-primary-900">SYSTEM STATUS: <span class="text-secondary-700">RECOVERABLE</span></p>
                    </div>
                </aside>
            </div>
        </main>

        <footer class="border-t border-surface-border bg-surface-canvas">
            <div class="mx-auto max-w-7xl px-4 py-5 font-mono text-[10px] uppercase tracking-[0.16em] text-primary-500 sm:px-6 lg:px-8">
                Ascent — Teknik Komputer 2026
            </div>
        </footer>
    </body>
</html>