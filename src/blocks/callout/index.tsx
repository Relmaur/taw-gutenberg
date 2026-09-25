/**
 * Callout: the example block that proves the Vite pipeline (ADR-0003).
 *
 * block.json is the single source of truth for the name and attributes; PHP
 * registers it through taw/core's Assets\Vite::block(), which loads this file
 * and style.scss from Vite.
 */
import { registerBlockType, type BlockConfiguration } from '@wordpress/blocks';
import metadata from './block.json';
import Edit from './edit';
import save from './save';
import type { CalloutAttributes } from './edit';

// block.json's JSON types are wider than BlockConfiguration's (e.g. category is a string).
registerBlockType(metadata as unknown as BlockConfiguration<CalloutAttributes>, { edit: Edit, save });
