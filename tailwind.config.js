/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: false, // Disable dark mode completely
  content: [
    './*.php',
    './inc/**/*.php',
    './templates/**/*.php',
    './shortcodes/**/*.php',
    './assets/js/**/*.js',
    './*.html'
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#eff6ff',
          100: '#dbeafe',
          500: '#3b82f6',
          600: '#2563eb',
          700: '#1d4ed8',
          800: '#1e40af',
          900: '#1e3a8a',
        },
        navy: {
          50: '#f0f4ff',
          100: '#e0e7ff',
          200: '#c7d2fe',
          300: '#a5b4fc',
          400: '#818cf8',
          500: '#6366f1',
          600: '#4f46e5',
          700: '#4338ca',
          800: '#3730a3',
          900: '#312e81',
          950: '#1e1b4b',
        }
      },
      fontFamily: {
        'inter': ['Inter', 'sans-serif'],
        'jakarta': ['"Plus Jakarta Sans"', 'sans-serif'],
        'lato': ['Lato', 'sans-serif'],
        'manrope': ['Manrope', 'sans-serif'],
        'marcellus': ['Marcellus', 'serif'],
        'lora': ['Lora', 'serif'],
      },
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
      }
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
    require('@tailwindcss/forms'),
    require('@tailwindcss/aspect-ratio'),
  ],
}
