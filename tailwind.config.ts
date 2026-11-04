import type { Config } from 'tailwindcss';

const config: Config = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './resources/**/*.ts',
        './src/**/*.{ts,tsx,js,jsx}',
    ],
    theme: {
        extend: {
            colors: {
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
            },
        },
    },
    plugins: [],
};

export default config;
