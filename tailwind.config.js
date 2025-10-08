import preset from './vendor/filament/support/tailwind.config.preset'

export default {
  presets: [preset],
  darkMode: 'class', // Enable class-based dark mode
  content: [
    './app/Filament/**/*.php',
    './resources/**/*.blade.php',
    './resources/views/filament/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
  ],
  theme: {
    extend: {
      // Custom animations for dark mode transitions
      animation: {
        'dark-mode-fade': 'fadeIn 0.2s ease-in-out',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
      },
    },
  },
}
