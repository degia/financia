import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

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
                primary: {
                    50: '#f5f5f5',
                    100: '#e5e5e5',
                    200: '#d4d4d4',
                    300: '#b0b0b0',
                    400: '#8a8a8a',
                    500: '#636363',
                    600: '#4a4a4a',
                    700: '#363636',
                    800: '#1f1f1f',
                    900: '#121212',
                    950: '#0a0a0a',
                },
                cream: {
                    50: '#fffef5',
                    100: '#fbefcb',
                    200: '#f5e0a0',
                    300: '#edd080',
                },
                navy: {
                    600: '#0a3b64',
                    700: '#0d4a7a',
                    800: '#10558a',
                    900: '#08304e',
                    950: '#041e32',
                },
            },
        },
    },

    plugins: [forms],
};
