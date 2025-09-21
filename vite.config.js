import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
  server: {
    host: true,              // dengarkan semua interface
    port: 5173,
    cors: {
      origin: ['https://berkemah.com'], // izinkan hanya domain produksi
      methods: ['GET','POST','PUT','PATCH','DELETE','OPTIONS'],
      allowedHeaders: ['Content-Type','Authorization','X-Requested-With'],
      credentials: false,
    },
    https: false, // biarkan false jika pakai ngrok yang sudah HTTPS
  },
});
