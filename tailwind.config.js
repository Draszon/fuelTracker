import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    safelist: [
        'bg-red-500',
        'bg-gray-500',
        'hover:bg-red-700',
        'hover:bg-gray-700',
        'animate-appear',
        'animate-disappear',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
        keyframes: {
            appear: {
                '0%': {maxHeight: '0px'},
                '100%': {maxHeight: '350px'},
            },
            disappear: {
                '0%': {maxHeight: '350px'},
                '100%': {maxHeight: '0px'},
            }
        },
        animation: {
            appear: 'appear .5s ease-in-out forwards',
            disappear: 'disappear .5s ease-in-out forwards',
        }
    },

    plugins: [forms, typography],
};
