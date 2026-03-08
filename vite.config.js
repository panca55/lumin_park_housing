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
        rollupOptions: {
            // Fix compatibility issues 
            external: ['fsevents'],
        },
        // Basic minification
        minify: true,
        // Target modern browsers
        target: 'es2018',
    },
    // Simpler dev server
    server: {
        hmr: {
            overlay: false
        }
    },
    // Basic CSS configuration
    css: {
        postcss: './postcss.config.js'
    }
});
