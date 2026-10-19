<x-filament-panels::page>
    <div class="flex items-center gap-2 text-sm">
        <button
            type="button"
            wire:click="goToDirectory('')"
            class="font-medium text-primary-600 hover:underline"
        >
            root
        </button>

        @php
            $segments = array_filter(explode('/', $this->getCurrentDirectory()));
            $accumulated = '';
        @endphp

        @foreach ($segments as $segment)
            @php $accumulated = trim($accumulated.'/'.$segment, '/'); @endphp
            <span class="text-gray-400">/</span>
            <button
                type="button"
                wire:click="goToDirectory('{{ $accumulated }}')"
                class="font-medium text-primary-600 hover:underline"
            >
                {{ $segment }}
            </button>
        @endforeach

        @if ($this->getCurrentDirectory() !== '')
            <button
                type="button"
                wire:click="goUp"
                class="ml-4 inline-flex items-center gap-1 text-gray-500 hover:text-gray-700"
            >
                <x-heroicon-o-arrow-up class="h-4 w-4" />
                Up
            </button>
        @endif
    </div>

    {{ $this->table }}
</x-filament-panels::page>
