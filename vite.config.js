import { defineConfig } from "vite";
import path from "path";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import AutoImport from "unplugin-auto-import/vite";
import Components from "unplugin-vue-components/vite";
import { ElementPlusResolver } from "unplugin-vue-components/resolvers";

export default defineConfig({
    plugins: [
        AutoImport({
            resolvers: [
                ElementPlusResolver({
                    importStyle: "scss", // Gunakan SCSS untuk konsistensi
                }),
            ],
        }),
        Components({
            extensions: ["vue", "md"],
            include: [/\.vue$/, /\.vue\?vue/, /\.md$/],
            resolvers: [
                ElementPlusResolver({
                    importStyle: "scss", // Gunakan SCSS untuk konsistensi
                }),
            ],
        }),
        laravel({
            input: ["resources/scss/app.scss", "resources/js/app.js"], // Gunakan SCSS
            refresh: true,
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
            "@": path.resolve(__dirname, "resources/js"),
            "~": path.resolve(__dirname, "resources/js"),
        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes("node_modules")) {
                        return id
                            .toString()
                            .split("node_modules/")[1]
                            .split("/")[0]
                            .toString();
                    }
                },
            },
        },
    },
    optimizeDeps: {
        include: [
            '@vueuse/core',
            'class-variance-authority',
            'clsx',
            'tailwind-merge'
        ]
    }
});