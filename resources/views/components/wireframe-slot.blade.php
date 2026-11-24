@props([
    'variant' => 'emblem', // 'emblem' | 'mascot' | 'banner' | 'moment' | 'empty' | 'error'
    'label' => null,
    'hint' => null,
])

<div {{ $attributes->merge(['class' => 'relative w-full h-full flex flex-col items-center justify-center overflow-hidden bg-canvas-base/75 p-6 text-center select-none']) }}>
    {{-- Subtle Geometric Honeycomb Accent --}}
    <svg class="pointer-events-none absolute inset-0 m-auto size-56 text-brand-gold/12" viewBox="0 0 200 200" fill="none" aria-hidden="true">
        <polygon points="100,14 174,57 174,143 100,186 26,143 26,57" stroke="currentColor" stroke-width="1" />
        <polygon points="100,40 152,70 152,130 100,160 48,130 48,70" stroke="currentColor" stroke-width="1" stroke-dasharray="4 4" />
    </svg>

    <div class="relative z-10 flex flex-col items-center gap-3">
        @if ($variant === 'mascot')
            <div class="size-16 rounded-2xl border border-brand-gold/30 bg-surface-card/80 flex items-center justify-center text-brand-gold">
                <svg class="size-8 stroke-[1.5] stroke-current" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 4.5 7.5v9L12 21l7.5-4.5v-9L12 3Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 10.5h.01M14.5 10.5h.01M9.5 14a3.5 3.5 0 0 0 5 0" />
                </svg>
            </div>
        @elseif ($variant === 'moment' || $variant === 'banner')
            <div class="size-14 rounded-2xl border border-white/15 bg-surface-card/80 flex items-center justify-center text-brand-gold">
                <svg class="size-7 stroke-[1.5] stroke-current" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="14" rx="2.5" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="m3 15 5-5 4 4 3-3 6 6" />
                    <circle cx="8.5" cy="9.5" r="1.25" fill="currentColor" stroke="none" />
                </svg>
            </div>
        @elseif ($variant === 'empty' || $variant === 'error')
            <div class="size-14 rounded-2xl border border-brand-red-hover/50 bg-brand-red/20 flex items-center justify-center text-brand-gold">
                <svg class="size-7 stroke-[1.5] stroke-current" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="6.5" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="m16 16 4.5 4.5" />
                </svg>
            </div>
        @else
            <div class="size-16 rounded-2xl border border-brand-gold/30 bg-surface-card/80 flex items-center justify-center text-brand-gold">
                <svg class="size-8 stroke-[1.5] stroke-current" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <polygon points="12,3 20,7.5 20,16.5 12,21 4,16.5 4,7.5" stroke-linejoin="round" />
                    <circle cx="12" cy="12" r="2.5" />
                </svg>
            </div>
        @endif

        @if ($label)
            <p class="font-display text-xs sm:text-sm font-medium text-text-secondary">
                {{ $label }}
            </p>
        @endif

        @if ($hint)
            <p class="font-body text-xs text-text-muted max-w-xs">
                {{ $hint }}
            </p>
        @endif
    </div>
</div>
