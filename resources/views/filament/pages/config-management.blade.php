<x-filament-panels::page>
    {{-- Bagian 1: Form passcode angkatan --}}
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div>
            <x-filament::button type="submit" icon="heroicon-o-check">
                Simpan Passcode
            </x-filament::button>
        </div>
    </form>

    {{-- Bagian 2: Rotasi epoch sesi --}}
    <x-filament::section
        heading="Rotasi Sesi (Auth Epoch)"
        description="Paksa seluruh mahasiswa yang sedang login untuk masuk ulang."
        icon="heroicon-o-arrow-path"
    >
        <div class="space-y-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Epoch saat ini:
                <span class="font-semibold text-gray-950 dark:text-white">{{ $this->getCurrentEpoch() }}</span>
            </p>

            <p class="text-sm text-gray-600 dark:text-gray-400">
                Setiap sesi mahasiswa membawa nomor epoch. Jika nomor itu tidak sama dengan epoch sistem,
                sesi dianggap tidak valid. Merotasi epoch menaikkan nomornya sebesar 1.
            </p>

            {{ $this->rotateEpochAction }}
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-panels::page>
