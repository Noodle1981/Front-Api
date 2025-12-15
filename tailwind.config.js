import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                headings: ['Poppins', 'sans-serif'], // Ejemplo de fuente personalizada
            },
            colors: {
                // *** ¡Aquí he añadido tu color naranja! ***
                'orange-xamanen': '#FF8E28', // Puedes usar bg-orange-xamanen, text-orange-xamanen, etc.

                // #FFAC4E Tu paleta de colores de marca (la mantenemos por si la usas en otras partes)
                    'primary': {
                        'light': '#00E3E9', // Principal
                        'DEFAULT': '#00E3E9',
                        'variant': '#166264', // Variante
                    },
                'secondary': '#4B5563',

                // Esquema de colores para el modo claro (si se usa en otras partes)
                    'background': '#272121', // Secundario fondo
                    'surface': '#221E1F',
                'text-main': '#15243aff',
                'text-muted': '#6B7280',

                // Esquema de colores para el modo oscuro (si se usa en otras partes)
                'dark-background': '#021745ff', // Un gris muy oscuro
                'dark-surface': '#ef680eff',    // Un gris un poco más claro para tarjetas
                'dark-text-main': '#F9FAFB',
                'dark-text-muted': '#9CA3AF',
            },
        },
    },

    plugins: [forms],
};