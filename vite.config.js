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
        host: 'localhost',
        port: 5173,
    }
})

// import dotenv from 'dotenv';

// dotenv.config();

// const appUrl = process.env.APP_URL ?? 'http://localhost:8000';
// const isNgrok = appUrl.includes('ngrok-free.dev');

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
//     ],
//     server: {
//         host: '0.0.0.0',
//         https: isNgrok, 
//         hmr: isNgrok
//         ? {
//             host: new URL(appUrl).hostname,
//             protocol: 'wss',
//             }
//         :{
//             host: 'localhost',
//             protocol: 'ws',
//         },
//     },
// });
