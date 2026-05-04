import path from 'node:path';
import { defineConfig } from 'vite';
import { mixManifestPlugin } from './typerocket.vite.js';

const publicPath = 'wordpress/assets/templates';
const entries = {
    theme: 'resources/assets/js/theme.js',
    admin: 'resources/assets/js/admin.js',
};
const styles = {
    'admin/admin.css': 'resources/assets/sass/admin.css',
    'theme/theme.css': 'resources/assets/sass/theme.css',
};

export default defineConfig({
    publicDir: false,
    build: {
        outDir: publicPath,
        emptyOutDir: false,
        manifest: false,
        sourcemap: false,
        rollupOptions: {
            input: entries,
            output: {
                entryFileNames: (chunk) => {
                    return path.posix.join(chunk.name, path.basename(chunk.facadeModuleId));
                },
                assetFileNames: '[name].[ext]',
                chunkFileNames: 'js/[name].js',
            },
        },
    },
    css: {
        devSourcemap: false,
    },
    plugins: [
        mixManifestPlugin({ publicPath, entries, styles }),
    ],
});
