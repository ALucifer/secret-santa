import { defineConfig } from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import vuePlugin from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig({
    server: {
        host: "0.0.0.0",
        port: 5173,
        hmr: {
            host: "localhost", // le host vu par le navigateur
            clientPort: 5173,             // le port public exposé
        },
    },
    plugins: [
        vuePlugin(),
        symfonyPlugin({
            stimulus: true,
            viteDevServerHostname: "localhost", // IMPORTANT
        }),
    ],
    resolve: {
        alias: {
            "@app": path.resolve(__dirname, "assets/vue"),
            "@js": path.resolve(__dirname, "assets/js"),
        },
    },
    build: {
        manifest: true,
        emptyOutDir: false,
        rollupOptions: {
            input: {
                app: "./assets/app.ts",
                menu: "./assets/menu.js",
                security: "./assets/security.js",
                profile: "./assets/profile.js",
                modal: "./assets/modal.js",
                "profile-incomplet": "./assets/profile-incomplet.js",
            },
        },
    },
});
