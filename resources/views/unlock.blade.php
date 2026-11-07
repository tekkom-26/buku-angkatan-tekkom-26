<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Buka Akses — Tekkom 26</title>
        @fonts
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        @livewireStyles
    </head>
    <body class="min-h-screen flex flex-col bg-brand-mist text-brand-deep antialiased">
        <x-navbar />

        <main class="flex-1 flex items-center justify-center px-4 py-16 relative overflow-hidden">

            {{-- ── Decorative organic wave shapes ── --}}
            {{-- Top-right large blob --}}
            <div class="absolute -top-32 -right-24 w-[420px] h-[420px] rounded-full bg-brand-cyan/12 blur-3xl"></div>
            {{-- Bottom-left medium blob --}}
            <div class="absolute -bottom-20 -left-20 w-[340px] h-[340px] rounded-full bg-brand-mint/18 blur-3xl"></div>
            {{-- Top-left small accent --}}
            <div class="absolute top-1/4 -left-10 w-[180px] h-[180px] rounded-full bg-brand-teal/8 blur-2xl"></div>
            {{-- Bottom-right subtle accent --}}
            <div class="absolute bottom-1/3 -right-10 w-[200px] h-[200px] rounded-full bg-brand-cyan/8 blur-2xl"></div>

            {{-- Organic wave SVG overlay (bottom) --}}
            <svg class="absolute bottom-0 left-0 right-0 w-full" viewBox="0 0 1440 200" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path d="M0,120 C360,180 720,40 1080,100 C1260,130 1380,160 1440,170 L1440,200 L0,200 Z" fill="url(#wave-gradient)" opacity="0.35"/>
                <path d="M0,150 C480,80 960,190 1440,140 L1440,200 L0,200 Z" fill="url(#wave-gradient-2)" opacity="0.25"/>
                <defs>
                    <linearGradient id="wave-gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#38BFC3" />
                        <stop offset="50%" stop-color="#176B70" />
                        <stop offset="100%" stop-color="#A5E6DF" />
                    </linearGradient>
                    <linearGradient id="wave-gradient-2" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="#A5E6DF" />
                        <stop offset="100%" stop-color="#38BFC3" />
                    </linearGradient>
                </defs>
            </svg>

            {{-- ── Glassmorphism Card ── --}}
            <div class="relative z-10 w-full max-w-md">

                <div class="backdrop-blur-md bg-white/70 border border-brand-teal/20 shadow-xl rounded-2xl p-8 sm:p-10">

                    {{-- Card Header --}}
                    <div class="text-center mb-8">
                        {{-- Icon circle --}}
                        <div class="w-16 h-16 mx-auto mb-5 rounded-2xl bg-gradient-to-br from-brand-teal to-brand-cyan flex items-center justify-center shadow-lg shadow-brand-teal/20">
                            <flux:icon icon="lock-closed" class="size-7 text-white" />
                        </div>

                        <h1 class="text-xl sm:text-2xl font-extrabold text-brand-deep tracking-tight">
                            Portal Direktori // {{ $cohortName ?? 'TEKKOM 2026' }}
                        </h1>
                        <p class="text-sm text-brand-deep/60 mt-2 leading-relaxed">
                            Masukkan Passcode Angkatan untuk Mengakses Data
                        </p>
                    </div>

                    {{-- Error Alert (hidden by default) --}}
                    <div id="error-alert" class="hidden mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                        <div class="flex items-start gap-3">
                            <flux:icon icon="exclamation-circle" class="size-5 text-red-500 shrink-0 mt-0.5" />
                            <div>
                                <p class="text-sm font-semibold text-red-700">Passcode tidak valid</p>
                                <p class="text-xs text-red-500/80 mt-0.5">Silakan coba lagi.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Success Alert (hidden by default) --}}
                    <div id="success-alert" class="hidden mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3">
                        <div class="flex items-start gap-3">
                            <flux:icon icon="check-circle" class="size-5 text-emerald-500 shrink-0 mt-0.5" />
                            <div>
                                <p class="text-sm font-semibold text-emerald-700">Passcode benar!</p>
                                <p class="text-xs text-emerald-500/80 mt-0.5">Mengalihkan ke Buku Angkatan...</p>
                            </div>
                        </div>
                    </div>

                    {{-- Passcode Form --}}
                    <form id="passcode-form" method="POST" class="space-y-5">

                        @csrf

                        {{-- Single Password Input --}}

                        <flux:field>
                            <flux:label class="!text-brand-deep/75 !text-xs font-medium">
                                Passcode Angkatan
                            </flux:label>

                            <flux:input
                                id="passcode-input"
                                name="passcode"
                                type="password"
                                viewable
                                placeholder="Masukkan passcode angkatan..."
                                autocomplete="off"
                                class="!rounded-xl !bg-white !border-gray-200 !shadow-sm focus:!shadow-md focus:!border-brand-teal/40 transition-all duration-200"
                                class:input="focus:outline-none"
                            />
                        </flux:field>


                        {{-- Submit Button --}}
                        <flux:button
                            id="submit-btn"
                            type="submit"
                            variant="primary"
                            class="w-full !py-3 !text-sm !text-brand-mist !font-semibold"
                            icon-trailing="arrow-right"
                        >
                            Masuk ke Buku Angkatan
                        </flux:button>
                    </form>

                    {{-- Helper text --}}
                    <p class="mt-6 text-center text-[11px] text-brand-deep/35 leading-relaxed">
                        Passcode dapat diperoleh dari koordinator angkatan atau panitia Buku Angkatan Teknik Komputer 2026.
                    </p>
                </div>

                {{-- Back link --}}
                <div class="text-center mt-6">
                    <a href="/" class="inline-flex items-center gap-1.5 text-xs font-medium text-brand-deep/45 hover:text-brand-teal transition-colors">
                        <flux:icon icon="arrow-left" class="size-3.5" />
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </main>

        @livewireScripts
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('passcode-form');
                const submitBtn = document.getElementById('submit-btn');
                const errorAlert = document.getElementById('error-alert');
                const successAlert = document.getElementById('success-alert');
                const passcodeInput = document.getElementById('passcode-input');

                form.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    const passcode = passcodeInput.value.trim();

                    // Basic validation
                    if (!passcode) {
                        showError();
                        return;
                    }

                    // Hide previous alerts
                    hideAlerts();

                    // Show loading state on button
                    setLoading(true);

                    try {
                        const response = await fetch('/api/auth/passcode', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            credentials: 'same-origin',
                            body: JSON.stringify({ passcode: form.passcode.value }),
                        });

                        if (response.ok) {
                            // Success
                            showSuccess();

                            // Redirect to directory after short delay
                            setTimeout(function () {
                                window.location.href = '/directory';
                            }, 1200);
                        } else if (response.status === 401 || response.status === 422) {
                            // Invalid passcode
                            showError();
                            passcodeInput.value = '';
                            passcodeInput.focus();
                        } else {
                            // Other error
                            showError();
                        }
                    } catch (err) {
                        // Network or server error — still show error for now
                        console.error('Passcode verification error:', err);
                        showError();
                    } finally {
                        setLoading(false);
                    }
                });

                function setLoading(loading) {
                    if (loading) {
                        submitBtn.setAttribute('disabled', 'true');
                        submitBtn.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="animate-spin size-4" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="32" stroke-dashoffset="32" stroke-linecap="round"/></svg> Memverifikasi...</span>';
                    } else {
                        submitBtn.removeAttribute('disabled');
                        submitBtn.innerHTML = 'Masuk ke Buku Angkatan';
                    }
                }

                function showError() {
                    errorAlert.classList.remove('hidden');
                    successAlert.classList.add('hidden');
                    // Shake animation on card
                    const card = form.closest('.rounded-2xl');
                    card.classList.add('animate-pulse');
                    setTimeout(() => card.classList.remove('animate-pulse'), 500);
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
        @fluxScripts
    </body>
</html>
