/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    './app/**/*.php',
    './public/assets/js/**/*.js',
  ],
  safelist: [
    'translate-x-full',
    'translate-x-0',
    'opacity-0',
    'opacity-100',
    'bg-blue-600',
    'bg-green-600',
    'bg-yellow-500',
    'bg-red-600',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};
