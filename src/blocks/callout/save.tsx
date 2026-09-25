import React from 'react';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { toneClass } from './tones';
import type { CalloutAttributes } from './edit';

export default function save({ attributes }: { attributes: CalloutAttributes }) {
    return (
        <div {...useBlockProps.save({ className: toneClass(attributes.tone) })} role="note">
            <RichText.Content tagName="p" value={attributes.content ?? ''} />
        </div>
    );
}
