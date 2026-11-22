@php
    /**
     * 26 Colony Nodes & Bioluminescent Particles (COMETS 2026)
     * Pure GPU-accelerated CSS animations (transform: translate3d & opacity).
     * Zero JavaScript runtime overhead, 60fps smooth rendering.
     */
    $particles = [
        // ── Left Flank Particles (0% - 25% X) ──
        ['type' => 'gold-orb', 'size' => 'size-2', 'pos' => 'left-[4%] top-[18%]', 'anim' => 'anim-colony-up-med', 'delay' => '0s'],
        ['type' => 'red-orb', 'size' => 'size-2.5', 'pos' => 'left-[8%] top-[42%]', 'anim' => 'anim-colony-orbit', 'delay' => '-3s'],
        ['type' => 'hex-gold', 'size' => 'size-4', 'pos' => 'left-[6%] top-[68%]', 'anim' => 'anim-colony-drift', 'delay' => '-5s'],
        ['type' => 'binary-gold', 'bit' => '1', 'pos' => 'left-[12%] top-[28%]', 'anim' => 'anim-colony-up-slow', 'delay' => '-2s'],
        ['type' => 'gold-orb', 'size' => 'size-1.5', 'pos' => 'left-[15%] top-[55%]', 'anim' => 'anim-colony-up-fast', 'delay' => '-6s'],
        ['type' => 'red-orb', 'size' => 'size-2', 'pos' => 'left-[19%] top-[82%]', 'anim' => 'anim-colony-up-med', 'delay' => '-1.5s'],
        ['type' => 'hex-red', 'size' => 'size-3.5', 'pos' => 'left-[22%] top-[14%]', 'anim' => 'anim-colony-drift', 'delay' => '-7s'],
        ['type' => 'binary-red', 'bit' => '0', 'pos' => 'left-[10%] top-[88%]', 'anim' => 'anim-colony-up-slow', 'delay' => '-9s'],

        // ── Center-Left & Mid Field Particles (26% - 50% X) ──
        ['type' => 'gold-orb', 'size' => 'size-2', 'pos' => 'left-[28%] top-[34%]', 'anim' => 'anim-colony-orbit', 'delay' => '-4s'],
        ['type' => 'red-orb', 'size' => 'size-1.5', 'pos' => 'left-[33%] top-[64%]', 'anim' => 'anim-colony-up-med', 'delay' => '-8s'],
        ['type' => 'gold-orb', 'size' => 'size-1.5', 'pos' => 'left-[38%] top-[22%]', 'anim' => 'anim-colony-up-slow', 'delay' => '-11s'],
        ['type' => 'hex-gold', 'size' => 'size-3.5', 'pos' => 'left-[44%] top-[78%]', 'anim' => 'anim-colony-drift', 'delay' => '-2.5s'],
        ['type' => 'red-orb', 'size' => 'size-2', 'pos' => 'left-[48%] top-[12%]', 'anim' => 'anim-colony-orbit', 'delay' => '-6.5s'],

        // ── Center-Right Field Particles (51% - 75% X) ──
        ['type' => 'gold-orb', 'size' => 'size-2', 'pos' => 'left-[54%] top-[46%]', 'anim' => 'anim-colony-up-med', 'delay' => '-5.5s'],
        ['type' => 'red-orb', 'size' => 'size-1.5', 'pos' => 'left-[60%] top-[26%]', 'anim' => 'anim-colony-up-fast', 'delay' => '-3.5s'],
        ['type' => 'hex-red', 'size' => 'size-4', 'pos' => 'left-[65%] top-[74%]', 'anim' => 'anim-colony-drift', 'delay' => '-10s'],
        ['type' => 'gold-orb', 'size' => 'size-1.5', 'pos' => 'left-[70%] top-[16%]', 'anim' => 'anim-colony-orbit', 'delay' => '-1s'],
        ['type' => 'binary-gold', 'bit' => '0', 'pos' => 'left-[73%] top-[58%]', 'anim' => 'anim-colony-up-slow', 'delay' => '-7.5s'],

        // ── Right Flank Particles (76% - 96% X) ──
        ['type' => 'red-orb', 'size' => 'size-2.5', 'pos' => 'left-[78%] top-[36%]', 'anim' => 'anim-colony-orbit', 'delay' => '-4.5s'],
        ['type' => 'gold-orb', 'size' => 'size-2', 'pos' => 'left-[82%] top-[66%]', 'anim' => 'anim-colony-up-med', 'delay' => '-2s'],
        ['type' => 'hex-gold', 'size' => 'size-4', 'pos' => 'left-[86%] top-[22%]', 'anim' => 'anim-colony-drift', 'delay' => '-8.5s'],
        ['type' => 'binary-red', 'bit' => '1', 'pos' => 'left-[89%] top-[48%]', 'anim' => 'anim-colony-up-slow', 'delay' => '-12s'],
        ['type' => 'gold-orb', 'size' => 'size-1.5', 'pos' => 'left-[91%] top-[78%]', 'anim' => 'anim-colony-up-fast', 'delay' => '-3s'],
        ['type' => 'red-orb', 'size' => 'size-2', 'pos' => 'left-[94%] top-[30%]', 'anim' => 'anim-colony-up-med', 'delay' => '-6s'],
        ['type' => 'hex-red', 'size' => 'size-3.5', 'pos' => 'left-[95%] top-[62%]', 'anim' => 'anim-colony-drift', 'delay' => '-1.2s'],
        ['type' => 'gold-orb', 'size' => 'size-2', 'pos' => 'left-[84%] top-[88%]', 'anim' => 'anim-colony-orbit', 'delay' => '-9.5s'],
    ];
@endphp

{{-- ==========================================================================
     26 COLONY FLOATING PARTICLES BACKDROP (PLAN A: PURE GPU CSS KEYFRAMES)
     ========================================================================== --}}
<div class="pointer-events-none absolute inset-0 overflow-hidden select-none" aria-hidden="true">
    @foreach ($particles as $p)
        @if ($p['type'] === 'gold-orb')
            <span
                class="colony-particle colony-particle-gold absolute rounded-full {{ $p['size'] }} {{ $p['pos'] }} {{ $p['anim'] }}"
                style="animation-delay: {{ $p['delay'] }};"
            ></span>
        @elseif ($p['type'] === 'red-orb')
            <span
                class="colony-particle colony-particle-red absolute rounded-full {{ $p['size'] }} {{ $p['pos'] }} {{ $p['anim'] }}"
                style="animation-delay: {{ $p['delay'] }};"
            ></span>
        @elseif ($p['type'] === 'hex-gold')
            <svg
                class="colony-particle absolute {{ $p['size'] }} {{ $p['pos'] }} {{ $p['anim'] }} text-brand-gold/45"
                style="animation-delay: {{ $p['delay'] }};"
                viewBox="0 0 24 24"
                fill="none"
            >
                <polygon points="12,2 21,7 21,17 12,22 3,17 3,7" stroke="currentColor" stroke-width="1.75" />
            </svg>
        @elseif ($p['type'] === 'hex-red')
            <svg
                class="colony-particle absolute {{ $p['size'] }} {{ $p['pos'] }} {{ $p['anim'] }} text-brand-red-hover/65"
                style="animation-delay: {{ $p['delay'] }};"
                viewBox="0 0 24 24"
                fill="none"
            >
                <polygon points="12,2 21,7 21,17 12,22 3,17 3,7" stroke="currentColor" stroke-width="1.75" />
            </svg>
        @elseif ($p['type'] === 'binary-gold')
            <span
                class="colony-particle absolute font-mono text-[10px] font-bold text-brand-gold/35 {{ $p['pos'] }} {{ $p['anim'] }}"
                style="animation-delay: {{ $p['delay'] }};"
            >{{ $p['bit'] }}</span>
        @elseif ($p['type'] === 'binary-red')
            <span
                class="colony-particle absolute font-mono text-[10px] font-bold text-brand-red-hover/55 {{ $p['pos'] }} {{ $p['anim'] }}"
                style="animation-delay: {{ $p['delay'] }};"
            >{{ $p['bit'] }}</span>
        @endif
    @endforeach
</div>
