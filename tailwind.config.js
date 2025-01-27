import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
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
                primary: '#0ea5e9',    // sky-500
                primaryDark: '#0369a1',  // sky-700
                primaryLight: '#38bdf8',  // sky-400
                success: '#22c55e',    // green-500
                error: '#ef4444',      // red-500
                warning: '#f59e0b',    // amber-500
                // Grey scale
                darkestGrey: '#111827',  // gray-900
                darkerGrey: '#374151',   // gray-700
                darkGrey: '#4B5563',     // gray-600
                grey: '#6B7280',         // gray-500
                lightGrey: '#9CA3AF',    // gray-400
                lighterGrey: '#D1D5DB',  // gray-300
                lightestGrey: '#F3F4F6', // gray-100
                white: '#FFFFFF',
            },
        },
    },

    plugins: [forms],
};
