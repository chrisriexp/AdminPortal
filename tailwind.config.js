/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    "./index.html",
    "./resources/views/PDF/commission.blade.php",
    "./resources/**/*.{vue,js,ts,jsx,tsx}"
  ],
  theme: {
    extend: {
      fontFamily: {
        'montserrat': ['Montserrat', 'sans-serif'],
        'pt-sans': ['PT Sans', 'sans-serif']
      },
      colors: {
        'custom-purple': '#A778E5',
        'custom-orange': '#F4812D',
        'custom-red': '#F42D2D',
        'custom-green': '#2FA52D',
        'custom-black': '#212121',
        'sidebar-bg': '#FBFBFB',
        'custom-yellow': '#FFC15E',

        'custom-blue': '#5080C7',
        'custom-gray': '#4c4c4c',
        'custom-light-blue': '#EFF3F6',
        'custom-light-blue-bg': '#E8F0FE',
        'custom-dark-blue': '#172340',
        'custom-bg': '#F5F5F5',
        'custom-light': '#DEDEDE',
        'custom-light-gray': '#939498',
        'custom-light-gray-bg': '#2E2E30',
        'custom-gray-bg': '#252628',
        'custom-dark-bg': '#1E1F21',
        'custom-dark-hover': 'rgba(255, 255, 255, 0.06)'
      },
      boxShadow: {
        'newdrop': 'rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;',
        "customdrop": 'rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px;',
        'purple-drop': '0px 0px 10px 0px rgba(167,120,229,0.6)'
      },
    },
    screens: {
      'sm': '640px',
      // => @media (min-width: 640px) { ... }

      'md': '768px',
      // => @media (min-width: 768px) { ... }

      'lg': '1024px',
      // => @media (min-width: 1024px) { ... }

      'xl': '1280px',
      // => @media (min-width: 1280px) { ... }

      '2xl': '1536px',
      // => @media (min-width: 1536px) { ... }
    },
  },
  plugins: [
    require("@tailwindcss/forms"),
    require("tailwindcss-inner-border")
  ],
}