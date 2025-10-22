import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig({
    plugins: [vue()],
    resolve: {
        alias: {
            "@app": path.resolve(__dirname, "../assets/vue"),
            "@js": path.resolve(__dirname, "../assets/js"),
        },
    },
});