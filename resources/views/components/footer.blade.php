@props([
    'activePage' => 'home', // 'home' | 'identity' | 'directory' | 'unlock' | 'student' | 'contact'
])

<footer class="border-t border-surface-border bg-surface-canvas text-primary-900 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-8 items-start">

            {{-- ── 1. Maskot Resmi Angkatan 2026 (stich-uiux/Footer.png) ── --}}
            <div class="lg:col-span-3 flex flex-col items-start">
                <div class="w-full max-w-[200px] aspect-square rounded-2xl border border-surface-border bg-surface-card relative flex items-center justify-center overflow-hidden shadow-2xs">
                    {{-- Diagonal wireframe cross (exact SVG wireframe from design) --}}
                    <svg class="absolute inset-0 size-full text-surface-border pointer-events-none" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">
                        <line x1="0" y1="0" x2="100" y2="100" stroke="currentColor" stroke-width="1" />
                        <line x1="100" y1="0" x2="0" y2="100" stroke="currentColor" stroke-width="1" />
                    </svg>
                    <span class="relative z-10 font-mono text-[10px] sm:text-[11px] text-primary-700 font-semibold text-center px-3 leading-tight select-none">
                        [X] [MASCOT:<br>GAJAH_CHIBI_TEKKOM]
                    </span>
                </div>
                <p class="font-mono text-xs text-primary-500 mt-3 select-none">
                    Maskot Resmi Angkatan 2026
                </p>
            </div>

            {{-- ── 2. Brand & Alamat Undip (stich-uiux/Footer.png) ── --}}
            <div class="lg:col-span-3 flex flex-col items-start">
                {{-- Circular logo badge [x] --}}
                <div class="size-8 rounded-full border border-surface-border bg-surface-card flex items-center justify-center font-mono text-xs text-primary-900 font-medium mb-3 shadow-2xs">
                    [x]
                </div>
                <h3 class="text-sm sm:text-base font-bold text-primary-900 tracking-tight mb-2">
                    ANGKATAN &mdash; Teknik Komputer 2026
                </h3>
                <p class="text-xs text-primary-700 leading-relaxed max-w-sm">
                    Departemen Teknik Komputer, Fakultas Teknik, Universitas Diponegoro, Jl. Prof. Soedarto, SH, Tembalang, Semarang.
                </p>
                <div class="mt-4">
                    <a
                        href="https://maps.google.com/?q=Departemen+Teknik+Komputer+Universitas+Diponegoro"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-primary-900 hover:text-secondary-700 transition-colors group"
                    >
                        <span>Google Maps</span>
                        <svg class="size-3 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M7 17l9.2-9.2M17 17V7H7" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- ── 3. Navigasi & Kanal Resmi (stich-uiux/Footer.png) ── --}}
            <div class="lg:col-span-3 flex flex-col space-y-6">
                {{-- // NAVIGASI --}}
                <div>
                    <p class="font-mono text-xs uppercase tracking-wider text-primary-500 mb-3">// NAVIGASI</p>
                    <ul class="space-y-2 text-xs">
                        <li>
                            <a
                                href="{{ route('landing') }}"
                                class="transition-colors {{ $activePage === 'home' ? 'text-primary-900 font-semibold' : 'text-primary-700 hover:text-primary-900' }}"
                            >
                                Beranda
                            </a>
                        </li>
                        <li>
                            <a
                                href="{{ route('identity') }}"
                                class="transition-colors {{ $activePage === 'identity' ? 'text-primary-900 font-semibold' : 'text-primary-700 hover:text-primary-900' }}"
                            >
                                Filosofi &amp; Identitas
                            </a>
                        </li>
                        <li>
                            <a
                                href="{{ route('directory.index') }}"
                                class="transition-colors {{ in_array($activePage, ['directory', 'unlock', 'student', 'students.show']) ? 'text-primary-900 font-semibold' : 'text-primary-700 hover:text-primary-900' }}"
                            >
                                Direktori Mahasiswa
                            </a>
                        </li>
                        <li>
                            <a
                                href="/#galeri"
                                class="text-primary-700 hover:text-primary-900 transition-colors"
                            >
                                Galeri Kegiatan
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- // KANAL_RESMI --}}
                <div>
                    <p class="font-mono text-xs uppercase tracking-wider text-primary-500 mb-3">// KANAL_RESMI</p>
                    <a
                        href="https://instagram.com/tekkom26"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1 text-xs text-primary-700 hover:text-primary-900 transition-colors group"
                    >
                        <span>Instagram (@tekkom26)</span>
                        <svg class="size-3 text-primary-500 group-hover:text-primary-900 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M7 17l9.2-9.2M17 17V7H7" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- ── 4. Telemetri Sistem Card (stich-uiux/Footer.png) ── --}}
            <div class="lg:col-span-3 flex flex-col">
                <p class="font-mono text-xs uppercase tracking-wider text-primary-500 mb-3">// TELEMETRI_SISTEM</p>
                <div class="rounded-2xl border border-surface-border bg-white p-5 space-y-2 font-mono text-xs text-primary-700 shadow-2xs">
                    <div>[BATCH_STATUS: ACTIVE]</div>
                    <div>[ANGKATAN_SPEC: TEKKOM_XXVI]</div>
                    <div>[SECURITY_LEVEL: VERIFIED]</div>
                    <div>[STUDENT_COUNT: 104_STUDENTS]</div>
                </div>
            </div>

        </div>

        {{-- ── Bottom Bar: Copyright (stich-uiux/Footer.png) ── --}}
        <div class="border-t border-surface-border mt-12 pt-6">
            <p class="font-mono text-xs text-primary-500">
                &copy; {{ date('Y') }} ANGKATAN Teknik Komputer Undip. All rights reserved.
            </p>
        </div>
    </div>
</footer>
