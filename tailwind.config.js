const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    prefix: 'tw-',
    important: true,
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#07102a',
                secondary: '#e3ecf1',
                accent: '#9C27B0',
                dark: '#1D1D1D',
                positive: '#16aa1b',
                negative: '#ff0000',
                info: '#0085ff',
                warning: '#f3b900'
            }
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
