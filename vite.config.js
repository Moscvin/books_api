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
        outDir: 'dist', // Schimbă directorul de ieșire la `dist`
        emptyOutDir: true, // Asigură-te că directorul este curățat la fiecare build
    },
});
