import React from 'react';
import { describe, expect, it, vi } from 'vitest';
import { fireEvent, render, screen } from '@testing-library/react';
import Edit from './edit';

describe('Callout block: edit', () => {
    it('shows the tone and changes it from the inspector', () => {
        const setAttributes = vi.fn();
        const { container } = render(<Edit attributes={{ tone: 'info' }} setAttributes={setAttributes} />);

        expect(container.querySelector('.is-tone-info[role="note"]')).not.toBeNull();

        fireEvent.change(screen.getByLabelText('Tone'), { target: { value: 'warning' } });
        expect(setAttributes).toHaveBeenCalledWith({ tone: 'warning' });
    });

    it('edits the text as a paragraph', () => {
        const setAttributes = vi.fn();
        render(<Edit attributes={{ tone: 'success', content: 'Saved' }} setAttributes={setAttributes} />);

        const text = screen.getByLabelText('Write the note…');
        expect(text).toHaveValue('Saved');
        expect(text).toHaveAttribute('data-tag-name', 'p');

        fireEvent.change(text, { target: { value: 'Saved!' } });
        expect(setAttributes).toHaveBeenCalledWith({ content: 'Saved!' });
    });
});
