import { describe, expect, it } from 'vitest';
import { renderToStaticMarkup } from 'react-dom/server';
import save from './save';

describe('Callout block: save', () => {
    it('saves static markup with the tone class', () => {
        const html = renderToStaticMarkup(
            save({ attributes: { tone: 'warning', content: 'Mind the <strong>gap</strong>' } }),
        );

        expect(html).toBe('<div class="is-tone-warning" role="note"><p>Mind the <strong>gap</strong></p></div>');
    });

    it('falls back to the info tone', () => {
        const html = renderToStaticMarkup(save({ attributes: { tone: undefined as never } }));

        expect(html).toContain('class="is-tone-info"');
    });
});
