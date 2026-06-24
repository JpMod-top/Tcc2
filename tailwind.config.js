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
    'app-nav-link--active',
    'app-stat-card--brand',
    'app-stat-card--warning',
    'app-stat-card--danger',
    'app-stat-card--success',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'Segoe UI', 'system-ui', '-apple-system', 'sans-serif'],
      },
      colors: {
        brand: {
          50: '#f0f4ff',
          100: '#e0e9ff',
          200: '#c7d6fe',
          300: '#a4b8fc',
          400: '#8093f8',
          500: '#6366f1',
          600: '#4f46e5',
          700: '#4338ca',
          800: '#3730a3',
          900: '#312e81',
          950: '#1e1b4b',
        },
      },
      boxShadow: {
        soft: '0 1px 3px rgba(15, 23, 42, 0.06), 0 8px 24px rgba(15, 23, 42, 0.04)',
        card: '0 1px 2px rgba(15, 23, 42, 0.04), 0 12px 32px rgba(15, 23, 42, 0.06)',
      },
      borderRadius: {
        xl: '0.875rem',
        '2xl': '1rem',
      },
    },
  },
  plugins: [],
};
