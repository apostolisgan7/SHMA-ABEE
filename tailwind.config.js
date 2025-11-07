/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './**/*.php',
        './src/**/*.js',
        './src/**/*.scss',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#171717',
                    50: '#f5f5f5',
                    100: '#ebebeb',
                    200: '#d6d6d6',
                    300: '#b3b3b3',
                    400: '#808080',
                    500: '#171717',
                    600: '#0d0d0d',
                    700: '#0a0a0a',
                    800: '#080808',
                    900: '#050505',
                },
                secondary: {
                    DEFAULT: '#17B83A',
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#17B83A',
                    600: '#0d9f2e',
                    700: '#0a7d25',
                    800: '#08661e',
                    900: '#07521a',
                },
                dark: {
                    50: '#f9fafb',
                    100: '#f3f4f6',
                    200: '#e5e7eb',
                    300: '#d1d5db',
                    400: '#9ca3af',
                    500: '#6b7280',
                    600: '#4b5563',
                    700: '#374151',
                    800: '#1f2937',
                    900: '#111827',
                },
                background: {
                    DEFAULT: '#ffffff',
                    light: '#f9f9f9',
                    dark: '#f0f0f0',
                },
                text: {
                    DEFAULT: '#333333',
                    light: '#666666',
                    lighter: '#999999',
                }
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
                display: ['Poppins', 'sans-serif'],
            },
            backgroundColor: theme => ({
                ...theme('colors'),
                body: '#ffffff',
            }),
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}