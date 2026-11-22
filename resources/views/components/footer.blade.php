@props([
    'activePage' => 'home', // 'home' | 'identity' | 'directory' | 'unlock' | 'student' | 'gallery' | 'contact'
])

<footer class="border-t border-white/10 bg-canvas-base text-text-secondary transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-start">

            {{-- ── Kolom Kiri: Box Logo Angkatan, Alamat Departemen Teknik Komputer Undip & Google Maps ── --}}
            <div class="lg:col-span-5 flex flex-col items-start space-y-4">
                <div class="flex items-center gap-3.5">
                    {{-- Box Logo COMETS Placeholder --}}
                    <div class="size-12 rounded-xl border border-brand-gold/30 bg-surface-card flex items-center justify-center font-mono text-[10px] text-brand-gold font-bold shrink-0">
                        <span class="sr-only">[X: COMETS Logo]</span>
                        <span aria-hidden="true">C26</span>
                    </div>
                    <div>
                        <p class="font-mono text-[10px] uppercase tracking-wider text-brand-gold">
                            // COMETS_2026 // COLONY_HQ
                        </p>
                        <h3 class="font-display text-base sm:text-lg font-bold text-text-primary tracking-tight">
                            ANGKATAN &mdash; Teknik Komputer 2026
                        </h3>
                    </div>
                </div>

                <p class="text-xs sm:text-sm text-text-secondary leading-relaxed max-w-md">
                    Departemen Teknik Komputer, Fakultas Teknik, Universitas Diponegoro, Jl. Prof. Soedarto, SH, Tembalang, Semarang.
                </p>

                <div class="pt-1">
                    <a
                        href="https://maps.google.com/?q=Departemen+Teknik+Komputer+Universitas+Diponegoro"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 font-mono text-xs uppercase tracking-wider font-semibold text-brand-gold hover:text-brand-gold-glow transition-colors group"
                    >
                        <span>Google Maps</span>
                        <span aria-hidden="true" class="transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5">&#8599;</span>
                    </a>
                </div>
            </div>

            {{-- ── Kolom Kanan: Menu // NAVIGASI, // KANAL_RESMI & // TELEMETRI_SISTEM ── --}}
            <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-3 gap-8">

                {{-- 1. Menu // NAVIGASI --}}
                <div>
                    <p class="font-mono text-xs uppercase tracking-wider text-brand-gold mb-3.5">// NAVIGASI</p>
                    <ul class="space-y-2.5 text-xs sm:text-sm font-body">
                        <li>
                            <a
                                href="{{ route('landing') }}"
                                class="transition-colors {{ $activePage === 'home' ? 'text-brand-gold font-semibold' : 'text-text-secondary hover:text-text-primary' }}"
                            >
                                Beranda
                            </a>
                        </li>
                        <li>
                            <a
                                href="{{ route('identity') }}"
                                class="transition-colors {{ $activePage === 'identity' ? 'text-brand-gold font-semibold' : 'text-text-secondary hover:text-text-primary' }}"
                            >
                                Filosofi &amp; Identitas
                            </a>
                        </li>
                        <li>
                            <a
                                href="{{ route('directory.index') }}"
                                class="transition-colors {{ in_array($activePage, ['directory', 'unlock', 'student', 'students.show']) ? 'text-brand-gold font-semibold' : 'text-text-secondary hover:text-text-primary' }}"
                            >
                                Direktori Mahasiswa
                            </a>
                        </li>
                        <li>
                            <a
                                href="{{ route('landing') }}#moments-section"
                                class="text-text-secondary hover:text-text-primary transition-colors"
                            >
                                Galeri Kegiatan
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- 2. Menu // KANAL_RESMI --}}
                <div>
                    <p class="font-mono text-xs uppercase tracking-wider text-brand-gold mb-3.5">// KANAL_RESMI</p>
                    <ul class="space-y-2.5 text-xs sm:text-sm font-body">
                        <li>
                            <a
                                href="https://instagram.com/tekkom26"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 text-text-secondary hover:text-brand-gold transition-colors group"
                            >
                                <span>Instagram (@tekkom26)</span>
                                <span aria-hidden="true" class="font-mono text-xs transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5">&#8599;</span>
                            </a>
                        </li>
                        <li>
                            <a
                                href="https://tekkom.ft.undip.ac.id"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 text-text-secondary hover:text-brand-gold transition-colors group"
                            >
                                <span>Kontak Departemen</span>
                                <span aria-hidden="true" class="font-mono text-xs transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5">&#8599;</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        {{-- ── Bottom Bar: Hak Cipta Resmi ── --}}
        <div class="border-t border-white/10 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="font-mono text-xs text-text-muted">
                &copy; 2026 ANGKATAN Teknik Komputer Undip. All rights reserved.
            </p>
            <p class="font-mono text-[11px] uppercase tracking-wider text-text-muted">
                COMETS // CONNECTED GEOMETRIC FUTURISM
            </p>
        </div>
    </div>
</footer>
