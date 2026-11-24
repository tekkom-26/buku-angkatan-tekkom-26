<x-layouts.app activePage="unlock" :title="'Buka Akses Direktori — ' . ($cohortName ?? 'COMETS 2026')">
    <main
        id="main-content"
        class="relative grow flex items-center justify-center px-4 py-12 sm:py-16 lg:py-20 bg-ambient-hero overflow-hidden"
    >
        <div
            x-data="{ showPassword: false }"
            class="relative z-10 w-full max-w-md"
        >
            <div
                id="unlock-terminal-card"
                class="rounded-2xl border border-white/15 bg-surface-card/90 backdrop-blur-md p-6 sm:p-9 relative overflow-hidden transition-colors"
            >
                {{-- Card Header & Lock Emblem --}}
                <div class="text-center mb-7">
                    <div class="size-14 mx-auto mb-5 rounded-2xl bg-canvas-base border border-brand-gold/35 flex items-center justify-center">
                        <svg class="size-6 text-brand-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </div>

                    <h1 class="font-display text-2xl sm:text-3xl font-bold text-text-primary tracking-tight">
                        Akses Direktori {{ $cohortName ?? 'TEKKOM 2026' }}
                    </h1>
                    <p class="font-body text-sm text-text-secondary mt-2 leading-relaxed">
                        Masukkan passcode angkatan untuk membuka profil lengkap mahasiswa COMETS 2026.
                    </p>
                </div>

                {{-- Error Alert --}}
                <div
                    id="error-alert"
                    role="alert"
                    class="hidden mb-5 rounded-xl border border-brand-red-hover/70 bg-brand-red/25 px-4 py-3 text-left"
                >
                    <div class="flex items-start gap-3">
                        <svg class="size-5 text-status-danger-text shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <div>
                            <p id="error-alert-title" class="font-display text-sm font-bold text-text-primary">
                                Passcode tidak valid
                            </p>
                            <p id="error-alert-desc" class="font-body text-xs text-text-secondary mt-0.5">
                                Silakan periksa kembali passcode angkatan dan coba lagi.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Success Alert --}}
                <div
                    id="success-alert"
                    role="status"
                    class="hidden mb-5 rounded-xl border border-brand-gold/50 bg-brand-gold/15 px-4 py-3 text-left"
                >
                    <div class="flex items-start gap-3">
                        <svg class="size-5 text-brand-gold shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        <div>
                            <p class="font-display text-sm font-bold text-brand-gold">
                                Passcode benar! Akses dibuka
                            </p>
                            <p class="font-body text-xs text-text-primary mt-0.5">
                                Mengalihkan ke Direktori Mahasiswa COMETS 2026...
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Passcode Verification Form --}}
                <form id="passcode-form" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label
                            for="passcode-input"
                            class="block font-display text-xs font-semibold text-brand-gold mb-2"
                        >
                            Passcode Angkatan
                        </label>

                        <div class="relative flex items-center">
                            <input
                                id="passcode-input"
                                name="passcode"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Masukkan passcode angkatan..."
                                autocomplete="current-password"
                                class="w-full min-h-12 rounded-xl border border-white/15 bg-surface-elevated pl-4 pr-12 py-3 font-mono text-sm text-text-primary placeholder:text-text-muted focus:border-brand-gold focus-visible:ring-2 focus-visible:ring-brand-gold/70 focus:outline-none transition-colors"
                            />

                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-1.5 size-10 flex items-center justify-center rounded-lg text-text-muted hover:text-brand-gold transition-colors cursor-pointer"
                                :aria-label="showPassword ? 'Sembunyikan passcode' : 'Tampilkan passcode'"
                            >
                                <svg x-show="!showPassword" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg x-cloak x-show="showPassword" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                                    <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                                    <line x1="2" x2="22" y1="2" y2="22" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button
                        id="submit-btn"
                        type="submit"
                        class="w-full min-h-12 py-3 px-6 rounded-xl bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary font-display font-semibold text-sm sm:text-base inline-flex items-center justify-center gap-2 transition-colors cursor-pointer"
                    >
                        <span>Masuk ke Buku Angkatan</span>
                        <span class="font-mono text-base leading-none" aria-hidden="true">&rarr;</span>
                    </button>
                </form>

                <div class="mt-6 pt-4 border-t border-white/10 text-center">
                    <p class="font-body text-xs text-text-muted leading-relaxed">
                        Passcode dapat diperoleh dari koordinator angkatan atau panitia Buku Angkatan Teknik Komputer 2026.
                    </p>
                </div>
            </div>

            <div class="text-center mt-6">
                <a
                    href="{{ route('landing') }}"
                    class="inline-flex items-center gap-2 min-h-11 px-4 py-2 rounded-xl bg-surface-card/80 border border-white/10 hover:border-brand-gold/40 font-display text-xs font-medium text-text-secondary hover:text-brand-gold transition-colors"
                >
                    <span aria-hidden="true">&larr;</span>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </main>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('passcode-form');
                const submitBtn = document.getElementById('submit-btn');
                const errorAlert = document.getElementById('error-alert');
                const errorTitle = document.getElementById('error-alert-title');
                const errorDesc = document.getElementById('error-alert-desc');
                const successAlert = document.getElementById('success-alert');
                const passcodeInput = document.getElementById('passcode-input');

                form.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    const passcode = passcodeInput.value.trim();

                    if (!passcode) {
                        showError('Passcode wajib diisi', 'Silakan masukkan passcode angkatan terlebih dahulu.');
                        passcodeInput.focus();
                        return;
                    }

                    hideAlerts();
                    setLoading(true);

                    try {
                        const response = await fetch('/api/auth/passcode', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            credentials: 'same-origin',
                            body: JSON.stringify({ passcode }),
                        });

                        if (response.ok) {
                            showSuccess();

                            setTimeout(function () {
                                window.location.href = '/directory';
                            }, 900);
                        } else if (response.status === 429) {
                            showError('Terlalu banyak percobaan', 'Batas percobaan terlampaui. Silakan tunggu beberapa menit lagi.');
                        } else {
                            showError('Passcode tidak valid', 'Silakan periksa kembali passcode angkatan dan coba lagi.');
                            passcodeInput.focus();
                            passcodeInput.select();
                        }
                    } catch (err) {
                        console.error('Passcode verification error:', err);
                        showError('Gangguan jaringan', 'Tidak dapat menghubungi server verifikasi. Silakan coba lagi.');
                    } finally {
                        setLoading(false);
                    }
                });

                function setLoading(loading) {
                    if (loading) {
                        submitBtn.setAttribute('disabled', 'true');
                        submitBtn.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="animate-spin size-4 text-brand-gold" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="32" stroke-dashoffset="32" stroke-linecap="round"/></svg><span>Memverifikasi...</span></span>';
                    } else {
                        submitBtn.removeAttribute('disabled');
                        submitBtn.innerHTML = '<span>Masuk ke Buku Angkatan</span><span class="font-mono text-base leading-none" aria-hidden="true">&rarr;</span>';
                    }
                }

                function showError(title, desc) {
                    if (errorTitle && title) errorTitle.textContent = title;
                    if (errorDesc && desc) errorDesc.textContent = desc;
                    errorAlert.classList.remove('hidden');
                    successAlert.classList.add('hidden');
                }

                function showSuccess() {
                    successAlert.classList.remove('hidden');
                    errorAlert.classList.add('hidden');
                }

                function hideAlerts() {
                    errorAlert.classList.add('hidden');
                    successAlert.classList.add('hidden');
                }
            });
        </script>
    @endpush
</x-layouts.app>
