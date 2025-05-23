/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,vue}",
  ],
  theme: {
    extend: {
      colors: {
        'bleu-profond': '#0A2540',
        'turquoise': '#00B8D4',
        'gris-fonce': '#263238',
        'gris-clair': '#F5F7FA',
        'succes': '#2DD36F',
        'avertissement': '#FFC409',
        'danger': '#EB445A',
        'information': '#3880FF',
      },
      fontFamily: {
        'inter': ['Inter', 'sans-serif'],
      }
    },
  },
  plugins: [],
}