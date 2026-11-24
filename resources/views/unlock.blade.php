@php
    $turnstileSiteKey = config('services.turnstile.site_key');
    $turnstileEnabled = filled($turnstileSiteKey) && filled(config('services.turnstile.secret_key'));
@endphp

<x-layouts.app activePage="unlock" :title="'Buka Akses Direktori — ' . ($cohortName ?? 'COMETS 2026')">
    <main
        id="main-content"
        class="relative grow flex items-center justify-center px-4 py-12 sm:py-16 lg:py-20 bg-ambient-hero overflow-hidden"
    >
        {{-- Decorative Background SVG: Hexagonal Security Shield & Circuit Traces --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden select-none" aria-hidden="true">
            {{-- Left Circuit Traces --}}
            <svg class="hidden lg:block absolute left-0 top-1/4 w-80 h-80 text-brand-gold/20" viewBox="0 0 320 320" fill="none">
                <path d="M0 80 H120 L160 120 H240" stroke="currentColor" stroke-width="1.25" />
                <path d="M0 170 H90 L130 210 H200" stroke="currentColor" stroke-width="1" stroke-dasharray="4 4" class="text-brand-red-hover/35" />
                <circle cx="240" cy="120" r="4" class="fill-brand-gold" />
                <circle cx="200" cy="210" r="3.5" class="fill-brand-red-hover" />
            </svg>

            {{-- Right Circuit Traces --}}
            <svg class="hidden lg:block absolute right-0 bottom-1/4 w-80 h-80 text-brand-red-hover/30" viewBox="0 0 320 320" fill="none">
                <path d="M320 100 H200 L160 140 H80" stroke="currentColor" stroke-width="1.25" />
                <path d="M320 190 H230 L190 230 H120" stroke="currentColor" stroke-width="1" stroke-dasharray="4 4" class="text-brand-gold/25" />
                <circle cx="80" cy="140" r="4" class="fill-brand-red-hover" />
                <circle cx="120" cy="230" r="3.5" class="fill-brand-gold" />
            </svg>

            {{-- Center Concentric Hexagon Watermark --}}
            <svg class="absolute inset-0 m-auto size-[520px] text-brand-gold/10" viewBox="0 0 260 260" fill="none">
                <polygon points="130,10 234,70 234,190 130,250 26,190 26,70" stroke="currentColor" stroke-width="1" />
                <polygon points="130,36 211,83 211,177 130,224 49,177 49,83" stroke="currentColor" stroke-width="1" stroke-dasharray="6 6" />
            </svg>
        </div>

        {{-- ── Colony Security Terminal Card ── --}}
        <div
            x-data="{ showPassword: false }"
            class="relative z-10 w-full max-w-md"
        >
            <div
                id="unlock-terminal-card"
                class="rounded-xl border border-brand-gold/35 bg-surface-card/90 backdrop-blur-md p-6 sm:p-9 relative overflow-hidden glow-gold-subtle transition-all duration-200"
            >
                {{-- Top Dual-Tone Circuit Line (Tech Gold + Blood Red) --}}
                <div class="absolute inset-x-0 top-0 h-1 flex" aria-hidden="true">
                    <div class="w-2/3 bg-brand-gold"></div>
                    <div class="w-1/3 bg-brand-red"></div>
                </div>

                {{-- Top Security Telemetry Strip --}}
                <div class="flex items-center justify-between gap-2 pb-4 mb-6 border-b border-white/10 font-mono text-[11px] uppercase tracking-wider">
                    <span class="text-brand-gold font-bold">
                        &gt; // 02_SECURITY_GATE
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded bg-brand-red border border-brand-red-hover text-text-primary font-bold">
                        <span class="size-1.5 rounded-full bg-brand-gold animate-pulse" aria-hidden="true"></span>
                        <span>[CLEARANCE: LOCKED]</span>
                    </span>
                </div>

                {{-- Card Header & Hexagonal Lock Emblem --}}
                <div class="text-center mb-7">
                    <div class="relative size-16 mx-auto mb-5 rounded-xl bg-canvas-base border border-brand-gold/40 flex items-center justify-center glow-gold-subtle">
                        <svg class="size-7 text-brand-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </div>

                    <h1 class="font-display text-2xl sm:text-3xl font-bold text-text-primary tracking-tight">
                        Portal Direktori // {{ $cohortName ?? 'TEKKOM 2026' }}
                    </h1>
                    <p class="font-body text-sm text-text-secondary mt-2 leading-relaxed">
                        Masukkan Passcode Angkatan untuk Mengakses Data Koloni COMETS 2026
                    </p>
                </div>

                {{-- Error Alert (hidden by default) --}}
                <div
                    id="error-alert"
                    role="alert"
                    class="hidden mb-5 rounded-r-lg rounded-l-xs border border-brand-red-hover border-l-4 border-l-brand-red bg-brand-red/30 px-4 py-3 text-left glow-red-subtle"
                >
                    <div class="flex items-start gap-3">
                        <svg class="size-5 text-brand-gold shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
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

                {{-- Success Alert (hidden by default) --}}
                <div
                    id="success-alert"
                    role="status"
                    class="hidden mb-5 rounded-r-lg rounded-l-xs border border-brand-gold border-l-4 border-l-brand-gold bg-brand-gold/15 px-4 py-3 text-left glow-gold-subtle"
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
                            class="block font-mono text-xs font-bold uppercase tracking-wider text-brand-gold mb-2"
                        >
                            &gt; Passcode Angkatan
                        </label>

                        <div class="relative flex items-center">
                            <input
                                id="passcode-input"
                                name="passcode"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Masukkan passcode angkatan..."
                                autocomplete="off"
                                class="w-full min-h-12 rounded-lg border border-white/15 bg-surface-elevated pl-4 pr-12 py-3 font-mono text-sm text-text-primary placeholder:text-text-muted focus:border-brand-gold focus:outline-none transition-colors"
                            />

                            {{-- Toggle Password Visibility Button --}}
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 p-1.5 rounded-md text-text-muted hover:text-brand-gold transition-colors cursor-pointer"
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

                    {{-- Cloudflare Turnstile Verification Widget --}}
                    @if ($turnstileEnabled)
                        <div class="space-y-2">
                            <p class="font-mono text-[11px] font-bold uppercase tracking-wider text-text-secondary">
                                &gt; Verifikasi Keamanan
                            </p>
                            <div class="rounded-lg border border-white/10 bg-canvas-base/60 p-2 flex items-center justify-center min-h-[72px]">
                                <div
                                    id="turnstile-widget"
                                    class="cf-turnstile w-full"
                                    data-sitekey="{{ $turnstileSiteKey }}"
                                    data-theme="dark"
                                    data-size="flexible"
                                ></div>
                            </div>
                        </div>
                    @endif

                    {{-- Submit Button (Primary Blood Red) --}}
                    <button
                        id="submit-btn"
                        type="submit"
                        class="w-full py-3.5 px-6 rounded-lg bg-brand-red hover:bg-brand-red-hover border border-brand-red-hover text-text-primary font-display font-semibold text-sm sm:text-base inline-flex items-center justify-center gap-2 transition-colors glow-red-subtle cursor-pointer"
                    >
                        <span>Masuk ke Buku Angkatan</span>
                        <span class="font-mono text-base leading-none" aria-hidden="true">&rarr;</span>
                    </button>
                </form>

                {{-- Helper Footer Note --}}
                <div class="mt-6 pt-4 border-t border-white/10 text-center space-y-2">
                    <p class="font-body text-xs text-text-muted leading-relaxed">
                        Passcode dapat diperoleh dari koordinator angkatan atau panitia Buku Angkatan Teknik Komputer 2026.
                    </p>
                </div>
            </div>

            {{-- Back to Landing Link --}}
            <div class="text-center mt-6">
                <a
                    href="{{ route('landing') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-surface-card/80 border border-white/10 hover:border-brand-gold/40 font-mono text-xs uppercase tracking-wider text-text-secondary hover:text-brand-gold transition-colors"
                >
                    <span aria-hidden="true">&larr;</span>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </main>

    @push('scripts')
        @if ($turnstileEnabled)
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
        @endif
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('passcode-form');
                const submitBtn = document.getElementById('submit-btn');
                const errorAlert = document.getElementById('error-alert');
                const errorTitle = document.getElementById('error-alert-title');
                const errorDesc = document.getElementById('error-alert-desc');
                const successAlert = document.getElementById('success-alert');
                const passcodeInput = document.getElementById('passcode-input');
                const card = document.getElementById('unlock-terminal-card');
                const turnstileEnabled = {{ $turnstileEnabled ? 'true' : 'false' }};

                function resetTurnstile() {
                    if (turnstileEnabled && window.turnstile && typeof window.turnstile.reset === 'function') {
                        window.turnstile.reset();
                    }
                }

                form.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    const passcode = passcodeInput.value.trim();

                    if (!passcode) {
                        showError('Passcode wajib diisi', 'Silakan masukkan passcode angkatan terlebih dahulu.');
                        return;
                    }

                    const payload = { passcode: form.passcode.value };

                    if (turnstileEnabled) {
                        const tokenInput = form.querySelector('[name="cf-turnstile-response"]');
                        const turnstileToken = tokenInput ? tokenInput.value.trim() : '';

                        if (!turnstileToken) {
                            showError('Verifikasi keamanan diperlukan', 'Silakan selesaikan verifikasi Cloudflare terlebih dahulu.');
                            return;
                        }

                        payload.cf_turnstile_response = turnstileToken;
                    }

                    hideAlerts();
                    setLoading(true);

                    try {
                        const response = await fetch('/api/auth/passcode', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            credentials: 'same-origin',
                            body: JSON.stringify(payload),
                        });

                        const data = await response.json().catch(() => null);

                        if (response.ok) {
                            showSuccess();

                            setTimeout(function () {
                                window.location.href = '/directory';
                            }, 900);
                        } else if (response.status === 429) {
                            showError('Terlalu banyak percobaan', 'Batas percobaan terlampaui. Silakan tunggu beberapa menit lagi.');
                            resetTurnstile();
                        } else if (response.status === 422) {
                            const captchaMsg = data?.error?.message || data?.errors?.cf_turnstile_response?.[0] || 'Verifikasi keamanan Cloudflare gagal. Silakan coba lagi.';
                            showError('Verifikasi keamanan gagal', captchaMsg);
                            resetTurnstile();
                        } else {
                            showError('Passcode tidak valid', 'Silakan periksa kembali passcode angkatan dan coba lagi.');
                            passcodeInput.value = '';
                            passcodeInput.focus();
                            resetTurnstile();
                        }
                    } catch (err) {
                        console.error('Passcode verification error:', err);
                        showError('Gangguan jaringan', 'Tidak dapat menghubungi server verifikasi. Silakan coba lagi.');
                        resetTurnstile();
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
                    if (card) {
                        card.classList.add('animate-pulse');
                        setTimeout(() => card.classList.remove('animate-pulse'), 500);
                    }
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
