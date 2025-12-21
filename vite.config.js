import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        outDir: 'public/build',   // hasil build masuk ke public/build
        manifest: true,           // generate manifest.json untuk Laravel
        emptyOutDir: true         // bersihkan folder sebelum build baru
    }
});
