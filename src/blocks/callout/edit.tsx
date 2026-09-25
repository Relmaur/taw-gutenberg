import React from 'react';
import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { TONES, toneClass, type Tone } from './tones';

// A type alias (not an interface), so it satisfies registerBlockType's Record<string, unknown>.
export type CalloutAttributes = {
    tone: Tone;
    content?: string;
};

interface EditProps {
    attributes: CalloutAttributes;
    setAttributes: (attributes: Partial<CalloutAttributes>) => void;
}

export default function Edit({ attributes, setAttributes }: EditProps) {
    const blockProps = useBlockProps({ className: toneClass(attributes.tone) });

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Tone', 'taw-gutenberg')}>
                    <SelectControl
                        label={__('Tone', 'taw-gutenberg')}
                        value={attributes.tone}
                        options={[...TONES]}
                        onChange={(tone: string) => setAttributes({ tone: tone as Tone })}
                        __nextHasNoMarginBottom
                    />
                </PanelBody>
            </InspectorControls>
            <div {...blockProps} role="note">
                <RichText
                    tagName="p"
                    value={attributes.content ?? ''}
                    onChange={(content: string) => setAttributes({ content })}
                    placeholder={__('Write the note…', 'taw-gutenberg')}
                />
            </div>
        </>
    );
}
