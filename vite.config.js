import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/home.css',
                'resources/css/login.css',
                'resources/js/login.js',
                'resources/css/forgot-password.css',
                'resources/css/reset-password.css',
                'resources/js/reset-password.js',
                'resources/css/usuarios.css',
                'resources/js/usuarios.js',
                'resources/css/perfil.css',
                'resources/js/perfil.js',
                'resources/css/categorias.css',
                'resources/js/categorias.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
