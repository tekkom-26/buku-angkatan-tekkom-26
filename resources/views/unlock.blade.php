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
    </head>
    <body class="min-h-screen bg-brand-mist text-brand-deep antialiased">
        <main id="main-content" class="mx-auto grid min-h-screen max-w-md content-center px-4 py-16">
            <section class="rounded-2xl bg-white p-8 shadow-lg">
                <h1 class="text-3xl font-extrabold">Masukkan Passcode</h1>
                <p class="mt-2 text-sm text-brand-deep/65">Passcode angkatan diperlukan untuk melihat direktori mahasiswa.</p>

                <form id="unlock-form" class="mt-6 space-y-4" novalidate>
                    <div>
                        <label for="passcode" class="block text-sm font-semibold">Passcode</label>
                        <input
                            id="passcode"
                            name="passcode"
                            type="password"
                            autocomplete="current-password"
                            required
                            aria-describedby="unlock-error"
                            class="mt-1 w-full rounded-lg border border-brand-deep/20 px-4 py-3 focus:border-brand-teal focus:outline-none focus:ring-2 focus:ring-brand-teal/40"
                        >
                    </div>
                    <p id="unlock-error" role="alert" class="hidden text-sm font-medium text-red-600"></p>
                    <button type="submit" class="w-full rounded-lg bg-brand-teal px-4 py-3 font-semibold text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-brand-teal/40">
                        Buka
                    </button>
                </form>
            </section>
        </main>

        <script>
            const form = document.getElementById('unlock-form');
            const errorBox = document.getElementById('unlock-error');

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                errorBox.classList.add('hidden');

                try {
                    const response = await fetch('/api/auth/passcode', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        credentials: 'same-origin',
                        body: JSON.stringify({ passcode: form.passcode.value }),
                    });

                    if (response.ok) {
                        window.location.href = @json(route('directory.index'));
                        return;
                    }

                    const body = await response.json().catch(() => null);
                    errorBox.textContent = body?.error?.message ?? 'Terjadi kesalahan, coba lagi.';
                } catch (e) {
                    errorBox.textContent = 'Tidak dapat terhubung ke server.';
                }

                errorBox.classList.remove('hidden');
            });
        </script>
    </body>
</html>
