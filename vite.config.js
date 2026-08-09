import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js' // Sesuaikan jika kamu pakai path lain
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0', // Mengizinkan akses dari jaringan lokal
        port: 8001,      // Port Vite sesuai dengan error di gambar
        hmr: {
            host: '192.168.1.6', // Ganti dengan IP lokal kamu yang ada di gambar
        },
        cors: true, // 👈 INI KUNCI UTAMANYA: Mengizinkan port 8000 mengambil JS dari port 8001
    },
});