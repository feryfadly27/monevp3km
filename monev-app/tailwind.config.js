import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                navy:  { DEFAULT: '#0F172A', dark: '#020617' },
                slate: { DEFAULT: '#64748B' },
                sage:  { DEFAULT: '#059669', light: '#D1FAE5' },
                surface: '#FFFFFF',
                page:  '#F8FAFC',
            },
            fontFamily: {
                sans:    ['DM Sans', ...defaultTheme.fontFamily.sans],
                heading: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                mono:    ['Fira Code', ...defaultTheme.fontFamily.mono],
            },
            borderRadius: {
                DEFAULT: '8px',
                md: '12px',
                lg: '16px',
            },
            boxShadow: {
                card: '0 2px 6px rgba(15,23,42,0.05)',
                elevated: '0 4px 16px rgba(15,23,42,0.07)',
            },
        },
    },
    plugins: [forms],
};
