/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: 'class',
    content: [
        "./*.php",
        "./includes/**/*.php",
        "./admin/**/*.php",
        "./assets/**/*.js",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: 'rgb(34, 139, 34)',
                    dark: 'rgb(25, 105, 25)',
                    light: 'rgb(46, 125, 50)',
                    accent: 'rgb(139, 195, 74)',
                },
            },
            fontFamily: {
                sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
            },
        },
    },
    plugins: [],
}
