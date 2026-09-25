/**
 * Vite config for TAW Gutenberg (ADR-0003).
 *
 * The PHP side is taw/core's TAW\Core\Assets\Vite (ADR-0006): it reads
 * dist/hot to find this dev server and dist/.vite/manifest.json for built
 * files. The shared plugins come from taw/core, so run `composer install`
 * before `npm run dev` / `npm run build`.
 */
import { existsSync, readdirSync } from 'node:fs';
import path from 'node:path';
import { defineConfig } from 'vite';

const tawVite = path.resolve(import.meta.dirname, 'vendor/taw/core/resources/vite/taw-vite.mjs');
if (!existsSync(tawVite)) {
    throw new Error('taw/core is not installed: run `composer install` in the theme folder first.');
}
const { hotFile, phpReload, wordpressExternals } = await import(tawVite);

/** Files in a block folder that become their own Vite entries. */
const BLOCK_ENTRIES = ['index.tsx', 'view.ts', 'style.scss', 'editor.scss'];

/**
 * One entry per asset of every block in src/blocks/<name>/. block.json refers
 * to them with `file:./index.tsx` etc., and Assets\Vite::block() maps each one
 * to its built file.
 */
function blockEntries() {
    const blocksDir = path.resolve(import.meta.dirname, 'src/blocks');
    const input = {};
    for (const block of readdirSync(blocksDir, { withFileTypes: true })) {
        if (!block.isDirectory()) {
            continue;
        }
        for (const file of BLOCK_ENTRIES) {
            const source = path.join(blocksDir, block.name, file);
            if (existsSync(source)) {
                input[`block-${block.name}-${path.parse(file).name}`] = source;
            }
        }
    }
    return input;
}

/**
 * Site-wide entries, enqueued by app/Setup/Assets.php. Add one here and in
 * Assets::FRONTEND / Assets::EDITOR together.
 */
const GLOBAL_ENTRIES = {
    // main: path.resolve(import.meta.dirname, 'src/js/main.ts'),
};

export default defineConfig(({ command }) => ({
    // Relative URLs in built CSS, so fonts/images resolve from the theme
    // folder. The dev server needs '/' (its origin comes from hotFile()).
    base: command === 'build' ? './' : '/',

    plugins: [
        hotFile({ path: 'dist/hot' }),
        wordpressExternals(),
        // Reloads the page when a template, part, PHP file or stylesheet
        // changes. Block styles are <link> tags WordPress prints, which Vite
        // can't hot-swap, so a reload is how SCSS edits show up.
        phpReload({ extensions: ['.php', '.html', '.scss'] }),
    ],

    /**
     * Classic JSX runtime (React.createElement): WordPress provides React as a
     * global, and `import React from 'react'` resolves to it.
     */
    oxc: {
        jsx: {
            runtime: 'classic',
            pragma: 'React.createElement',
            pragmaFrag: 'React.Fragment',
        },
    },

    build: {
        outDir: 'dist',
        emptyOutDir: true,
        manifest: true,
        rolldownOptions: {
            input: { ...blockEntries(), ...GLOBAL_ENTRIES },
            output: {
                format: 'es',
                entryFileNames: '[name]-[hash].js',
                chunkFileNames: '[name]-[hash].js',
                assetFileNames: '[name]-[hash][extname]',
            },
        },
    },

    server: {
        // taw-theme's dev server usually has 5173. Any free port works:
        // hotFile() records the one Vite actually got.
        port: 5174,
        /**
         * The WordPress site (another origin) loads modules from here, which
         * needs CORS. Only local dev origins: `cors: true` would let any
         * website read the source while the dev server runs.
         */
        cors: {
            origin: /^https?:\/\/(localhost|127\.0\.0\.1|[a-z0-9-]+\.local)(:\d+)?$/,
        },
    },

    test: {
        environment: 'jsdom',
        setupFiles: ['./tests/js/setup.ts'],
        include: ['src/**/*.test.{ts,tsx}', 'tests/js/**/*.test.{ts,tsx}'],
        clearMocks: true,
    },
}));
