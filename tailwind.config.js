import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                bg: '#0b0f1a',
                sidebar: '#1a212e',
                card: '#161c2b',
                'card-hover': '#1e2638',
                accent: '#dc4a3c',
                teal: '#2dd4bf',
                'teal-dark': '#283142',
                success: '#34d399',
                'text-secondary': '#94a3b8',
                border: '#283142',
                'navbar-left': '#7a1f28',
                'navbar-right': '#131a2b',
            },
        },
    },

    plugins: [forms],
};
