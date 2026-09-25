import { __ } from '@wordpress/i18n';

export type Tone = 'info' | 'success' | 'warning';

/** The tones block.json allows, with their labels. Keep in sync with its "enum". */
export const TONES: ReadonlyArray<{ value: Tone; label: string }> = [
    { value: 'info', label: __('Information', 'taw-gutenberg') },
    { value: 'success', label: __('Success', 'taw-gutenberg') },
    { value: 'warning', label: __('Warning', 'taw-gutenberg') },
];

/** The class that carries a tone's colours (see style.scss). */
export function toneClass(tone: Tone | undefined): string {
    return `is-tone-${tone ?? 'info'}`;
}
