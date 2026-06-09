import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/filament/admin/theme.css',
        'resources/js/supabase.js',
        'resources/js/filament/admin/attendance-notifications.js',
      ],
      refresh: ['app/Livewire/**'],
    }),
    tailwindcss(),
  ],
})
