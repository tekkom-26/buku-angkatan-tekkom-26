@props([
    'activePage' => 'home',
])

@php
    $isDirectoryActive = in_array($activePage, ['directory', 'student', 'students.show']);
@endphp

<footer class="border-t border-white/10 bg-canvas-base text-text-secondary transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-start">

            {{-- Left Column: Cohort Identity & Address --}}
            <div class="lg:col-span-6 flex flex-col items-start space-y-4">
                <div class="flex items-center gap-3.5">
                    <div
                        class="size-11 rounded-xl border border-brand-gold/30 bg-surface-card flex items-center justify-center overflow-hidden p-1 shrink-0"
                        aria-hidden="true"
                    >
                        <img
                            src="{{ asset('assets/Placeholder.png') }}"
                            alt=""
                            class="size-full object-contain select-none"
                        />
                    </div>
                    <div>
                        <p class="font-display text-xs font-medium text-brand-gold">
                            COMETS 2026
                        </p>
                        <h3 class="font-display text-base sm:text-lg font-bold text-text-primary tracking-tight">
                            Teknik Komputer 2026 &mdash; Universitas Diponegoro
                        </h3>
                    </div>
                </div>

                <p class="text-xs sm:text-sm text-text-secondary leading-relaxed max-w-md">
                    Departemen Teknik Komputer, Fakultas Teknik, Universitas Diponegoro, Jl. Prof. Soedarto, SH, Tembalang, Semarang.
                </p>

                <div>
                    <a
                        href="https://maps.google.com/?q=Departemen+Teknik+Komputer+Universitas+Diponegoro"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center min-h-7 gap-1.5 font-display text-xs font-semibold text-brand-gold hover:text-brand-gold-glow transition-colors group"
                    >
                        <span>Buka di Google Maps</span>
                        <span aria-hidden="true" class="transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5">&#8599;</span>
                    </a>
                </div>
            </div>

            {{-- Right Columns: 2 Balanced Navigation Groups --}}
            <div class="lg:col-span-6 grid grid-cols-1 sm:grid-cols-2 gap-8 lg:justify-items-end">

                <div class="w-full sm:w-auto">
                    <p class="font-display text-xs font-semibold uppercase tracking-wider text-brand-gold mb-3.5">Navigasi</p>
                    <ul class="space-y-1.5 text-sm font-body">
                        <li>
                            <a
                                href="{{ route('landing') }}"
                                class="inline-flex items-center min-h-7 py-0.5 transition-colors {{ $activePage === 'home' ? 'text-brand-gold font-semibold' : 'text-text-secondary hover:text-text-primary' }}"
                            >
                                Beranda
                            </a>
                        </li>
                        <li>
                            <a
                                href="{{ route('identity') }}"
                                class="inline-flex items-center min-h-7 py-0.5 transition-colors {{ $activePage === 'identity' ? 'text-brand-gold font-semibold' : 'text-text-secondary hover:text-text-primary' }}"
                            >
                                Filosofi &amp; Identitas
                            </a>
                        </li>
                        <li>
                            <a
                                href="{{ route('directory.index') }}"
                                class="inline-flex items-center min-h-7 py-0.5 transition-colors {{ $isDirectoryActive ? 'text-brand-gold font-semibold' : 'text-text-secondary hover:text-text-primary' }}"
                            >
                                Direktori Mahasiswa
                            </a>
                        </li>
                        <li>
                            <a
                                href="{{ route('landing') }}#moments-section"
                                class="inline-flex items-center min-h-7 py-0.5 text-text-secondary hover:text-text-primary transition-colors"
                            >
                                Galeri Kegiatan
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="w-full sm:w-auto">
                    <p class="font-display text-xs font-semibold uppercase tracking-wider text-brand-gold mb-3.5">Kanal Resmi</p>
                    <ul class="space-y-1.5 text-sm font-body">
                        <li>
                            <a
                                href="https://instagram.com/tekkom26"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center min-h-7 py-0.5 gap-1.5 text-text-secondary hover:text-brand-gold transition-colors group"
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
                                class="inline-flex items-center min-h-7 py-0.5 gap-1.5 text-text-secondary hover:text-brand-gold transition-colors group"
                            >
                                <span>Website Departemen</span>
                                <span aria-hidden="true" class="font-mono text-xs transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5">&#8599;</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        {{-- Bottom Bar --}}
        <div class="border-t border-white/10 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="font-body text-xs text-text-muted">
                &copy; 2026 COMETS &mdash; Teknik Komputer Universitas Diponegoro.
            </p>
            <p class="font-display text-xs text-text-muted">
                Angkatan 2026
            </p>
        </div>
    </div>
</footer>
