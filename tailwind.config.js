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
                // Nuevo esquema de colores
                'brand-dark': '#0C263B',      // Navbar y Footer
                'brand-accent': '#FE9192',    // Enlaces activos, elementos destacados
                'btn-start': '#F7838F',       // Inicio gradiente botón
                'btn-end': '#FCB5AA',         // Fin gradiente botón
                
                // Mantenemos blanco y negro estándar de Tailwind (white, black)

                // Compatibilidad con código existente (si es necesario maperalo a los nuevos)
                'primary': '#0C263B', 
                'secondary': '#FE9192',
            },
        },
    },

    plugins: [forms],
};