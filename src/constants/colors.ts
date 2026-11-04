/**
 * Design Token & Color Palette Constants
 * Single Source of Truth untuk Token Warna Buku Angkatan Tekkom 2026.
 *
 * Catatan:
 * - Nilai HEX di sini merupakan baseline token saat ini.
 * - Untuk styling UI di Blade/JSX/HTML, SELALU gunakan kelas Tailwind semantik (misal: bg-secondary-500, text-primary-900).
 * - Jika ada penyesuaian shade warna angkatan, cukup sesuaikan CSS Variables di `:root` stylesheet global.
 */

export const COLOR_PALETTE = {
    primary: {
        50: '#f4f1f4',
        100: '#e2dce2',
        500: '#907990',
        700: '#786178',
        800: '#645064',
        900: '#514051', // Baseline text & kontras struktural utama
    },
    secondary: {
        50: '#f2f8f7',
        100: '#e0efec',
        300: '#aed4ce',
        500: '#80bab2', // Sky Wash - Action & link aktif utama
        700: '#548981',
        900: '#3b655f',
    },
    highlight: {
        100: '#f5f6db',
        300: '#e4e79b',
        500: '#d4d86a', // Butter Cream - Badge & highlight accent
        700: '#9fa436',
        900: '#6b6f1e',
    },
    surface: {
        canvas: '#FAFAFB', // Kanvas latar belakang utama
        subtle: '#f5f4f6', // Latar netral lembut
        card: '#e6e3e8',   // Snow White - Kartu bento & container sekunder
        border: 'rgba(81, 64, 81, 0.12)', // Hairline standard divider
    },
} as const;

export const COLOR_VARIABLES = {
    primary: {
        50: 'var(--color-primary-50)',
        100: 'var(--color-primary-100)',
        500: 'var(--color-primary-500)',
        700: 'var(--color-primary-700)',
        800: 'var(--color-primary-800)',
        900: 'var(--color-primary-900)',
    },
    secondary: {
        50: 'var(--color-secondary-50)',
        100: 'var(--color-secondary-100)',
        300: 'var(--color-secondary-300)',
        500: 'var(--color-secondary-500)',
        700: 'var(--color-secondary-700)',
        900: 'var(--color-secondary-900)',
    },
    highlight: {
        100: 'var(--color-highlight-100)',
        300: 'var(--color-highlight-300)',
        500: 'var(--color-highlight-500)',
        700: 'var(--color-highlight-700)',
        900: 'var(--color-highlight-900)',
    },
    surface: {
        canvas: 'var(--color-surface-canvas)',
        subtle: 'var(--color-surface-subtle)',
        card: 'var(--color-surface-card)',
        border: 'var(--color-surface-border)',
    },
} as const;

export type ColorFamily = keyof typeof COLOR_PALETTE;
export type PrimaryWeight = keyof typeof COLOR_PALETTE.primary;
export type SecondaryWeight = keyof typeof COLOR_PALETTE.secondary;
export type HighlightWeight = keyof typeof COLOR_PALETTE.highlight;
export type SurfaceVariant = keyof typeof COLOR_PALETTE.surface;

export type ColorToken =
    | `primary-${PrimaryWeight}`
    | `secondary-${SecondaryWeight}`
    | `highlight-${HighlightWeight}`
    | `surface-${SurfaceVariant}`;

/**
 * Helper untuk mengambil nama CSS Variable secara programmatic.
 * Contoh: getCssVar('primary', 900) => 'var(--color-primary-900)'
 */
export function getCssVar<F extends ColorFamily>(
    family: F,
    weight: keyof (typeof COLOR_PALETTE)[F]
): string {
    return `var(--color-${String(family)}-${String(weight)})`;
}
