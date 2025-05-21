import vue from '@vitejs/plugin-vue';
import autoprefixer from 'autoprefixer';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'node:path';
import path from 'path';
import tailwindcss from 'tailwindcss';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
            url: 'http://192.168.1.102:8000',
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
        },
    },
    css: {
        postcss: {
            plugins: [tailwindcss, autoprefixer],
        },
    },
    // server: {
    //     host: true, // This allows external connections
    //     // origin: 'http://192.168.1.102:8000',

    //     hmr: {
    //         host: '192.168.1.102', // Your machine's local IP address
    //     },
    // },

    server: {
        host: '0.0.0.0',
        hmr: {
            host: '0.0.0.0',
        },
        port: 5173,
        strictPort: true,
        origin: 'http://192.168.1.102:8000', // Your local IP
    },
});
