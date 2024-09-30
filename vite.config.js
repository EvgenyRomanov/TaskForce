import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/normalize.css',
                'resources/css/landing.css',
                'resources/css/style.css',
            ],
            refresh: true,
        }),
    ],
});