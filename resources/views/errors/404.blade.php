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
    <body class="min-h-screen bg-brand-mist text-brand-deep antialiased">
        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-10 focus:rounded-lg focus:bg-white focus:px-4 focus:py-3 focus:font-semibold focus:shadow-lg">
            Lewati ke konten utama
        </a>

        <header class="border-b border-brand-deep/10 bg-white">
            <div class="mx-auto flex max-w-6xl items-center px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('directory.index') }}" class="inline-flex items-center gap-3 rounded-md font-semibold text-brand-deep">
                    <span class="grid size-10 place-items-center rounded-lg bg-brand-teal text-lg text-white" aria-hidden="true">A</span>
                    Tekkom 26 <span class="text-sm font-medium text-brand-deep/45">Ascent 2026</span>
                </a>
            </div>
        </header>

        <main id="main-content" tabindex="-1" class="mx-auto grid min-h-[70vh] max-w-6xl content-center px-4 py-16 sm:px-6 lg:px-8">
            <section class="max-w-2xl">
                <p class="font-mono text-sm font-semibold text-brand-teal">ERROR 404</p>
                <h1 class="mt-3 text-4xl font-extrabold leading-tight text-brand-deep sm:text-5xl">Halaman ini tidak ditemukan.</h1>
                <p class="mt-4 max-w-xl text-base leading-relaxed text-brand-deep/65">
                    Tautan mungkin sudah berubah atau alamatnya kurang tepat. Cari mahasiswa dari Direktori, atau kembali ke halaman sebelumnya.
                </p>

                <form method="GET" action="{{ route('directory.index') }}" role="search" class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <label for="student-search" class="sr-only">Cari nama atau NIM mahasiswa</label>
                    <input id="student-search" name="search" type="search" placeholder="Nama atau NIM mahasiswa" class="min-h-12 min-w-0 flex-1 rounded-lg border border-brand-deep/20 bg-white px-4 text-brand-deep placeholder:text-brand-deep/40 focus:border-brand-teal">
                    <button type="submit" class="min-h-12 rounded-lg bg-brand-teal px-5 font-semibold text-white hover:bg-brand-deep">
                        Cari di Direktori
                    </button>
                </form>

                <div class="mt-5 flex flex-wrap items-center gap-5 text-sm font-semibold">
                    <a href="{{ route('directory.index') }}" class="text-brand-teal hover:text-brand-deep">Buka Direktori Mahasiswa</a>
                    <button type="button" class="text-brand-deep/60 hover:text-brand-deep" onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '{{ route('directory.index') }}'; }">
                        Kembali ke halaman sebelumnya
                    </button>
                </div>
            </section>
        </main>

        <footer class="border-t border-brand-deep/10 bg-white">
            <div class="mx-auto max-w-6xl px-4 py-5 text-xs text-brand-deep/50 sm:px-6 lg:px-8">
                Ascent — Teknik Komputer 2026
            </div>
        </footer>
    </body>
</html>