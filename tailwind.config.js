const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        "./resources/**/*.css",
        './node_modules/tw-elements/dist/js/**/*.js',
    ],

    safelist: [
        {
            pattern: /bg-(yellow|green|blue|red|purple|indigo|gray|pink)-(100|200|300|400|500)/
        },
        {
            pattern: /text-(yellow|green|blue|red|purple|indigo|gray|pink)-(100|200|300|400|500)/
        }
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [        
        require('tw-elements/dist/plugin')
    ],
};
