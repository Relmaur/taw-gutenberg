import { beforeEach, describe, expect, it, vi } from 'vitest';
import metadata from './block.json';
import { TONES } from './tones';

describe('Callout block: registration', () => {
    beforeEach(async () => {
        // index.tsx registers as a side effect of being imported.
        vi.resetModules();
        await import('./index');
    });

    it('registers block.json itself, so name and attributes have one source of truth', () => {
        expect(window.wp.blocks.registerBlockType).toHaveBeenCalledTimes(1);
        expect(window.wp.blocks.registerBlockType).toHaveBeenCalledWith(
            expect.objectContaining({ name: 'taw-gutenberg/callout', attributes: metadata.attributes }),
            expect.objectContaining({ edit: expect.any(Function), save: expect.any(Function) }),
        );
    });
});

describe('Callout block: block.json contract', () => {
    it('uses API v3 in the taw-gutenberg/ namespace (the editing-policy allow pattern)', () => {
        expect(metadata.apiVersion).toBe(3);
        expect(metadata.name.startsWith('taw-gutenberg/')).toBe(true);
    });

    it('loads its assets as file: sources, which taw/core Assets\\Vite::block() maps to Vite', () => {
        expect(metadata.editorScript).toBe('file:./index.tsx');
        expect(metadata.style).toBe('file:./style.scss');
    });

    it('allows exactly the tones the inspector offers', () => {
        expect(metadata.attributes.tone.enum).toEqual(TONES.map((tone) => tone.value));
        expect(TONES.map((tone) => tone.value)).toContain(metadata.attributes.tone.default);
    });
});
