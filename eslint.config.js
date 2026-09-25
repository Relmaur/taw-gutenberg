/**
 * ESLint flat config. Linting finds bugs; Prettier owns formatting, so
 * eslint-config-prettier (last) turns off rules that would argue with it.
 */
import { defineConfig, globalIgnores } from 'eslint/config';
import js from '@eslint/js';
import tseslint from 'typescript-eslint';
import react from 'eslint-plugin-react';
import reactHooks from 'eslint-plugin-react-hooks';
import prettier from 'eslint-config-prettier';
import globals from 'globals';

export default defineConfig([
    globalIgnores(['dist/', 'vendor/', 'coverage/', 'node_modules/']),

    js.configs.recommended,
    tseslint.configs.recommended,

    {
        files: ['src/**/*.{js,ts,tsx}', 'tests/js/**/*.{ts,tsx}'],
        extends: [react.configs.flat.recommended, reactHooks.configs.flat.recommended],
        languageOptions: { globals: globals.browser },
        // The React version WordPress core ships.
        settings: { react: { version: '18.3' } },
        rules: {
            // Blocks are typed with TypeScript, not PropTypes.
            'react/prop-types': 'off',
            // Classic JSX runtime: every JSX file needs React in scope.
            'react/react-in-jsx-scope': 'error',
        },
    },

    {
        files: ['*.config.js'],
        languageOptions: { globals: globals.node },
    },

    prettier,
]);
