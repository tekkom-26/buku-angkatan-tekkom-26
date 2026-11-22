<x-layouts.app activePage="error" :title="'Halaman Tidak Ditemukan — Tekkom 26'">
    <main id="main-content" tabindex="-1" class="relative grow w-full bg-ambient-hero py-10 sm:py-16 overflow-hidden">
        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Telemetry Status Strip --}}
            <div class="mb-8 grid grid-cols-2 rounded-xl border border-white/10 bg-surface-card/90 sm:grid-cols-4 overflow-hidden">
                <div class="border-r border-b sm:border-b-0 border-white/10 px-4 py-4 sm:px-5">
                    <p class="font-mono text-[10px] uppercase tracking-wider text-text-muted">STATUS</p>
                    <p class="mt-1.5 font-mono text-xs font-bold text-brand-red-hover">404 / NOT FOUND</p>
                </div>
                <div class="border-b sm:border-b-0 sm:border-r border-white/10 px-4 py-4 sm:px-5">
                    <p class="font-mono text-[10px] uppercase tracking-wider text-text-muted">MEMORY</p>
                    <p class="mt-1.5 font-mono text-xs font-bold text-text-primary">UNALLOCATED</p>
                </div>
                <div class="border-r border-white/10 px-4 py-4 sm:px-5">
                    <p class="font-mono text-[10px] uppercase tracking-wider text-text-muted">NODE</p>
                    <p class="mt-1.5 font-mono text-xs font-bold text-brand-gold">COMETS_ERR_07</p>
                </div>
                <div class="px-4 py-4 sm:px-5">
                    <p class="font-mono text-[10px] uppercase tracking-wider text-text-muted">RECOVERY</p>
                    <p class="mt-1.5 font-mono text-xs font-bold text-brand-gold">AVAILABLE</p>
                </div>
            </div>

            {{-- Main 404 Split Bento Card --}}
            <div class="grid overflow-hidden rounded-xl border border-brand-red-hover/60 bg-surface-card/90 lg:grid-cols-[1.5fr_1fr] glow-red-subtle relative">
                {{-- Top Dual Circuit Bar --}}
                <div class="absolute inset-x-0 top-0 h-1 flex" aria-hidden="true">
                    <div class="w-1/2 bg-brand-red"></div>
                    <div class="w-1/2 bg-brand-gold"></div>
                </div>

                <section class="border-b border-white/10 p-7 sm:p-10 lg:border-b-0 lg:border-r">
                    <p class="font-mono text-xs font-bold uppercase tracking-wider text-brand-gold">
                        &gt; // 07_ERR_404 // MEMORY_SEGMENTATION_FAULT
                    </p>
                    <h1 class="mt-4 max-w-2xl font-display text-6xl font-bold leading-none tracking-tight text-text-primary sm:text-8xl">
                        404<span class="text-brand-gold">.</span>
                    </h1>
                    <h2 class="mt-6 max-w-xl font-display text-2xl font-bold leading-tight tracking-tight text-text-primary sm:text-3xl">
                        Halaman ini tidak ditemukan.
                    </h2>
                    <p class="mt-3 max-w-xl font-body text-sm leading-relaxed text-text-secondary sm:text-base">
                        Simpul alamat yang kamu cari belum dialokasikan di jaringan koloni COMETS 2026. Gunakan jalur pemulihan di bawah untuk kembali ke sistem utama.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                        <a
                            href="{{ route('landing') }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-lg bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover px-5 font-display text-sm font-semibold text-text-primary transition-colors glow-red-subtle"
                        >
                            &larr; Kembali ke Beranda Utama
                        </a>
                        <a
                            href="{{ route('directory.index') }}"
                            class="inline-flex min-h-11 items-center justify-center rounded-lg border border-brand-gold/40 bg-brand-gold/10 hover:bg-brand-gold/20 px-5 font-display text-sm font-semibold text-brand-gold transition-colors"
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
                            class="min-h-11 min-w-0 flex-1 rounded-lg border border-white/15 bg-surface-elevated px-4 font-mono text-sm text-text-primary placeholder:text-text-muted focus:border-brand-gold focus:outline-none"
                        >
                        <button
                            type="submit"
                            class="min-h-11 rounded-lg bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover px-5 font-display text-sm font-semibold text-text-primary transition-colors cursor-pointer"
                        >
                            Cari di Direktori
                        </button>
                    </form>
                </section>

                <aside class="flex flex-col justify-between bg-canvas-base/85 p-7 sm:p-10">
                    <div>
                        <div class="flex items-center justify-between border-b border-white/10 pb-5">
                            <p class="font-mono text-[10px] uppercase tracking-wider text-text-muted">ERROR_DIAGNOSTIC</p>
                            <span class="rounded-md bg-brand-red border border-brand-red-hover px-3 py-1 font-mono text-[10px] font-bold text-text-primary">
                                TRACE_404
                            </span>
                        </div>

                        <div class="mx-auto mt-8 flex aspect-square w-full max-w-[190px] flex-col items-center justify-between rounded-xl border border-brand-gold/30 bg-surface-card p-3 glow-gold-subtle">
                            <span class="w-full text-left font-mono text-[8px] uppercase tracking-wider text-brand-gold">
                                [X] [ILLUSTRATION: 404_MASCOT_LOST_1:1]
                            </span>
                            <div class="relative flex size-full items-center justify-center overflow-hidden rounded-lg border border-dashed border-white/15 p-2">
                                <img
                                    src="{{ asset('/assets/Placeholder.png') }}"
                                    alt="[X: Bee-ner Mascot 404 — Halaman Tidak Ditemukan]"
                                    class="h-full w-full object-contain select-none"
                                    loading="lazy"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 space-y-2.5 font-mono text-[11px] leading-5 text-text-secondary">
                        <p class="text-brand-gold">[ERR_STATUS // 0x0194_NOT_FOUND]</p>
                        <p><span class="text-brand-gold">TRACE</span> / route_not_found</p>
                        <p><span class="text-brand-gold">CAUSE</span> / unallocated_address</p>
                        <p><span class="text-brand-gold">ACTION</span> / reroute_to_home</p>
                        <p class="border-t border-white/10 pt-3 text-text-primary">
                            SYSTEM STATUS: <span class="text-brand-gold font-bold">RECOVERABLE</span>
                        </p>
                    </div>
                </aside>
            </div>

        </div>
    </main>
</x-layouts.app>
