<x-layouts.app activePage="error" :title="'Halaman Tidak Ditemukan — Tekkom 26'">
    <main id="main-content" tabindex="-1" class="relative grow w-full bg-ambient-hero py-12 sm:py-20 overflow-hidden">
        <div class="relative z-10 mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            <div class="grid overflow-hidden rounded-2xl border border-white/10 bg-surface-card/85 lg:grid-cols-[1.5fr_1fr]">

                <section class="border-b border-white/10 p-7 sm:p-10 lg:border-b-0 lg:border-r">
                    <p class="font-mono text-xs font-bold uppercase tracking-widest text-status-danger-text">
                        Error 404 &middot; Halaman Tidak Ditemukan
                    </p>

                    <h1 class="mt-4 font-display text-6xl font-bold leading-none tracking-tight text-text-primary sm:text-8xl">
                        404<span class="text-brand-gold">.</span>
                    </h1>

                    <h2 class="mt-5 max-w-xl font-display text-2xl font-bold leading-tight tracking-tight text-text-primary sm:text-3xl">
                        Halaman ini tidak ditemukan.
                    </h2>

                    <p class="mt-3 max-w-xl font-body text-sm leading-relaxed text-text-secondary sm:text-base">
                        Alamat yang kamu tuju tidak tersedia atau sudah dipindahkan. Gunakan tautan di bawah untuk kembali ke beranda atau mencari profil mahasiswa di direktori.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                        <a
                            href="{{ route('landing') }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover px-5 font-display text-sm font-semibold text-text-primary transition-colors"
                        >
                            &larr; Kembali ke Beranda Utama
                        </a>
                        <a
                            href="{{ route('directory.index') }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-xl border border-brand-gold/40 bg-brand-gold/10 hover:bg-brand-gold/20 px-5 font-display text-sm font-semibold text-brand-gold transition-colors"
                        >
                            Jelajahi Direktori Mahasiswa &rarr;
                        </a>
                    </div>

                    <form method="GET" action="{{ route('directory.index') }}" role="search" class="mt-10 flex flex-col gap-3 border-t border-white/10 pt-6 sm:flex-row">
                        <label for="student-search" class="sr-only">Cari nama atau NIM mahasiswa</label>
                        <input
                            id="student-search"
                            name="search"
                            type="search"
                            placeholder="Cari nama atau NIM mahasiswa"
                            class="min-h-11 min-w-0 flex-1 rounded-xl border border-white/15 bg-surface-elevated px-4 font-body text-sm text-text-primary placeholder:text-text-muted focus:border-brand-gold focus-visible:ring-2 focus-visible:ring-brand-gold/70 focus:outline-none"
                        >
                        <button
                            type="submit"
                            class="min-h-11 rounded-xl bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover px-5 font-display text-sm font-semibold text-text-primary transition-colors cursor-pointer"
                        >
                            Cari di Direktori
                        </button>
                    </form>
                </section>

                <aside class="flex flex-col justify-between bg-canvas-base/75 p-7 sm:p-10">
                    <div class="flex-1 flex items-center justify-center">
                        <div class="aspect-square w-full max-w-[240px] rounded-2xl border border-white/10 overflow-hidden">
                            <x-wireframe-slot
                                variant="error"
                                label="Rute Tidak Tersedia"
                                hint="Tekkom 26 · COMETS"
                            />
                        </div>
                    </div>
                </aside>

            </div>

        </div>
    </main>
</x-layouts.app>
