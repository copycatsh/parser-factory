/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,jsx}'],
  theme: {
    extend: {
      colors: {
        newsprint: '#F5F0E8',
        ink: '#1A1A1A',
        gold: '#B8860B',
        rule: '#C8C0B0',
      },
      fontFamily: {
        playfair: ['"Playfair Display"', 'Georgia', 'serif'],
        baskerville: ['"Libre Baskerville"', 'Baskerville', 'serif'],
        mono: ['"JetBrains Mono"', 'Menlo', 'monospace'],
      },
    },
  },
  plugins: [],
};
