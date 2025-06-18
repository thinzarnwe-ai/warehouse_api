const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require('tailwindcss/colors')
/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./src/**/*.{html,js}",
    ],

    theme: {
        borderRadius: {
            'none': '0',
            'sm': '0.125rem',
            DEFAULT: '0.25rem',
            DEFAULT: '4px',
            'md': '10px',
            'lg': '30px',
            'full': '9999px',
            'large': '30px',
          },
          fontWeight: {
            semibold: '600',
            bold:'700'
          },
        extend: {
            colors: {
                cyan: colors.cyan,
                primary: '#0891B2',
                header:  '#11b5e4',
                dark:'#001021',
                sky: colors.sky,
                yellow: colors.yellow,
                teal: colors.teal,
                fuchsia: colors.fuchsia,
                rose: colors.rose,
                gray: colors.gray,
                slate:colors.slate,
                orange:colors.orange,
                lime:colors.lime
              },

        },
    },
    variants: {},
    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
