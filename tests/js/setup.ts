/**
 * Vitest setup: runs before every test file.
 */
import { createRequire } from 'node:module';
import { afterEach } from 'vitest';
import { cleanup } from '@testing-library/react';
import '@testing-library/jest-dom/vitest';
import { installWpGlobals } from './wp-globals';

/**
 * The REAL React on window, as WordPress provides it. `import React from
 * 'react'` goes through wordpressExternals and reads window.React, so it must
 * exist first. require() skips Vite's plugins and returns the same instance
 * @testing-library/react uses.
 */
const require = createRequire(import.meta.url);
Object.assign(window, { React: require('react'), ReactDOM: require('react-dom') });

installWpGlobals();

afterEach(() => {
    cleanup();
});
