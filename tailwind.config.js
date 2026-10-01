import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.jsx',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    navy: '#123A52',
                    teal: '#1E7F8C',
                    gold: '#E3A23A',
                    paper: '#F6F3EA',
                }
            },
            fontFamily: {
                sans: ['"Work Sans"', ...defaultTheme.fontFamily.sans],
                heading: ['"Fraunces"', ...defaultTheme.fontFamily.serif],
            },
            animation: {
                'marquee': 'marquee 80s linear infinite',
            },
            keyframes: {
                marquee: {
                    '0%': { transform: 'translateX(0%)' },
                    '100%': { transform: 'translateX(-100%)' },
                }
            },
        },
    },

    plugins: [forms],
};
