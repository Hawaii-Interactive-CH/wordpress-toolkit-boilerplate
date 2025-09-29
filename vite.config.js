import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import vue from "@vitejs/plugin-vue";
import liveReload from "vite-plugin-live-reload";
import sass from "sass";
import { resolve, dirname } from "path";
import { fileURLToPath } from "url";
import { config } from "dotenv";
import wordpressAlias from 'vite-plugin-wordpress-alias';
// Load environment variables
config();

// Define __dirname for ES module
const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

// https://vitejs.dev/config/
export default defineConfig({
    base: './',
    plugins: [wordpressAlias(), react(), vue(), liveReload(__dirname + "/**/*.php")],
    assetsInclude: ['**/*.woff', '**/*.woff2', '**/*.ttf', '**/*.otf', '**/*.eot', '**/*.svg', '**/*.png', '**/*.jpg', '**/*.jpeg', '**/*.gif', '**/*.webp'],
    server: {
        host: "0.0.0.0",
        port: 5173,
        cors: true,
        strictPort: true,
        hmr: {
            host: "localhost",
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                implementation: sass,
            },
        },
    },
    build: {
        // output dir for production build
        outDir: resolve(__dirname, "./toolkit/public"),
        emptyOutDir: true,

        // emit manifest so PHP can find the hashed files
        manifest: true,

        // our entry
        rollupOptions: {
            input: {
                app: resolve(__dirname, "./src/javascript/app.js"),
                blocks: resolve(__dirname, "./src/scss/blocks.scss"),
            },
            output: {
                entryFileNames: "js/[name].[hash].js",
                chunkFileNames: "js/[name].[hash].js",
                assetFileNames: (assetInfo) => {
                    const fileName = assetInfo.names?.[0] || 'unknown';
                    if (fileName.endsWith(".css")) {
                        return "css/[name].[hash].css";
                    }
                    // Handle font files  
                    if (fileName.match(/\.(woff|woff2|eot|ttf|otf)$/)) {
                        return "fonts/[name].[hash][extname]";
                    }
                    // Handle images
                    if (fileName.match(/\.(png|jpe?g|gif|svg|webp)$/)) {
                        return "images/[name].[hash][extname]";
                    }
                    return "assets/[name].[hash][extname]";
                },
            },
        },

        // minifying switch
        minify: true,
        write: true,
    },
    resolve: {
        alias: {
            "@": resolve(__dirname, "./src/"),
            "@js": resolve(__dirname, "./src/javascript"),
            "@blocks": resolve(__dirname, "./src/javascript/blocks"),
            "@components": resolve(__dirname, "./src/components"),
            "@hooks": resolve(__dirname, "./src/javascript/hooks"),
            "@utils": resolve(__dirname, "./src/javascript/utils"),
            "@scss": resolve(__dirname, "./src/scss"),
            "@fonts": resolve(__dirname, "./src/scss/assets/fonts"),
        },
    },
});
