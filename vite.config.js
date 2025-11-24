import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true, // Hot reload
        }),
    ],
    server: {
        host: "localhost",
        port: 5173,
        strictPort: false,

        // 🔥 Watcher super ringan
        watch: {
            ignored: [
                "**/node_modules/**",
                "**/vendor/**",
                "**/public/**",
                "**/storage/**",
                "**/bootstrap/**",
                "**/.git/**",
                "**/.idea/**",
                "**/.vscode/**",
            ],
            usePolling: false, // Kurangi CPU
            interval: 300, // Cek perubahan tiap 300ms (lebih ringan)
        },

        // 🧠 Hentikan rebuild besar jika error
        hmr: {
            overlay: true,
        },
    },
    optimizeDeps: {
        // Cegah rebuild massal dependencies
        exclude: ["@tailwindcss/forms", "@tailwindcss/typography"],
    },
    build: {
        outDir: "public/build",
        emptyOutDir: true,
        sourcemap: false, // Nonaktifkan sourcemap (hemat CPU & disk)
    },
});
