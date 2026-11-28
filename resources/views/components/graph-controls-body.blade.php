<div class="space-y-4">
    <div class="space-y-3">
        <p class="font-display text-[11px] font-semibold uppercase tracking-wider text-brand-gold">
            Forces
        </p>

        <label class="block space-y-1">
            <div class="flex items-center justify-between text-[11px]">
                <span class="font-body text-text-secondary">Repel Force</span>
                <span class="font-mono text-brand-gold" x-text="repelStrength"></span>
            </div>
            <input
                type="range"
                min="50"
                max="320"
                step="5"
                x-model.number="repelStrength"
                class="w-full accent-brand-gold cursor-pointer"
            />
        </label>

        <label class="block space-y-1">
            <div class="flex items-center justify-between text-[11px]">
                <span class="font-body text-text-secondary">Link Distance</span>
                <span class="font-mono text-brand-gold" x-text="linkDistance"></span>
            </div>
            <input
                type="range"
                min="70"
                max="260"
                step="5"
                x-model.number="linkDistance"
                class="w-full accent-brand-gold cursor-pointer"
            />
        </label>
    </div>

    <div class="pt-3 border-t border-white/10 space-y-2.5">
        <p class="font-display text-[11px] font-semibold uppercase tracking-wider text-brand-gold">
            Display
        </p>

        <label class="flex items-center justify-between gap-2 text-xs text-text-secondary cursor-pointer">
            <span>Tampilkan Garis Koneksi</span>
            <input type="checkbox" x-model="showLinks" class="size-4 accent-brand-gold cursor-pointer" />
        </label>

        <label class="flex items-center justify-between gap-2 text-xs text-text-secondary cursor-pointer">
            <span>Label Nama Mahasiswa</span>
            <input type="checkbox" x-model="showLabels" class="size-4 accent-brand-gold cursor-pointer" />
        </label>
    </div>

    <div class="pt-3 border-t border-white/10 grid grid-cols-2 gap-2">
        <button
            type="button"
            @click="graph && graph.reheat()"
            class="min-h-9 px-2.5 py-1.5 rounded-lg bg-surface-elevated border border-white/10 hover:border-brand-gold/50 font-display text-[11px] font-semibold text-text-primary cursor-pointer"
        >
            Re-heat
        </button>
        <button
            type="button"
            @click="graph && graph.resetCamera()"
            class="min-h-9 px-2.5 py-1.5 rounded-lg bg-surface-elevated border border-white/10 hover:border-brand-gold/50 font-display text-[11px] font-semibold text-brand-gold cursor-pointer"
        >
            Reset View
        </button>
    </div>
</div>
