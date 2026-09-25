/**
 * A tiny fake `window.wp` for tests.
 *
 * In WordPress, `@wordpress/*` imports resolve to the browser globals core
 * loads (taw/core's wordpressExternals, also used by Vitest). These stubs are
 * plain HTML that expose the props our code passes (value, onChange, labels),
 * so tests check our wiring without the real block editor. When a block uses
 * a new component, add a stub here.
 */
import { createRequire } from 'node:module';
import type { ReactNode } from 'react';
import { vi } from 'vitest';

const require = createRequire(import.meta.url);
const React: typeof import('react') = require('react');

interface RichTextStubProps {
    tagName?: string;
    value?: string;
    onChange: (value: string) => void;
    placeholder?: string;
}

interface SelectControlStubProps {
    label: string;
    value?: string;
    options: Array<{ value: string; label: string }>;
    onChange: (value: string) => void;
}

function RichText({ tagName = 'div', value, onChange, placeholder }: RichTextStubProps) {
    return (
        <input
            aria-label={placeholder}
            data-tag-name={tagName}
            value={value ?? ''}
            onChange={(event) => onChange(event.target.value)}
        />
    );
}
RichText.Content = function RichTextContent({ tagName = 'div', value }: { tagName?: string; value?: string }) {
    return React.createElement(tagName, { dangerouslySetInnerHTML: { __html: value ?? '' } });
};

function useBlockProps(props: Record<string, unknown> = {}) {
    return props;
}
useBlockProps.save = (props: Record<string, unknown> = {}) => props;

const blockEditor = {
    useBlockProps,
    RichText,
    InspectorControls: ({ children }: { children: ReactNode }) => (
        <aside data-testid="inspector-controls">{children}</aside>
    ),
};

const components = {
    PanelBody: ({ title, children }: { title: string; children: ReactNode }) => (
        <fieldset>
            <legend>{title}</legend>
            {children}
        </fieldset>
    ),
    SelectControl: ({ label, value, options, onChange }: SelectControlStubProps) => (
        <label>
            {label}
            <select value={value} onChange={(event) => onChange(event.target.value)}>
                {options.map((option) => (
                    <option key={option.value} value={option.value}>
                        {option.label}
                    </option>
                ))}
            </select>
        </label>
    ),
};

const blocks = { registerBlockType: vi.fn() };

const i18n = { __: (text: string) => text };

const fakeWp = { blockEditor, components, blocks, i18n, element: React };

declare global {
    interface Window {
        wp: typeof fakeWp;
    }
}

export function installWpGlobals(): void {
    window.wp = fakeWp;
}
