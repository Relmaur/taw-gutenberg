<?php

declare(strict_types=1);

namespace TAW\Gutenberg\Setup;

use TAW\Core\Boot;
use TAW\Gutenberg\Contracts\Bootable;

/**
 * Turns on taw/core's data layer — and only that (taw/core ADR-0003).
 *
 * This theme is a data-only taw/core consumer: it gets the schema registry
 * (taw-schema/*.json → post types, taxonomies, fieldsets, options pages),
 * REST-registered field meta and content import/export, but none of the
 * classic-theme toolkit (PHP blocks, Vite, Alpine, Performance tweaks) —
 * everything visual is Gutenberg's own.
 *
 * Booted at after_setup_theme priority 0 rather than here in register(): it
 * keeps register() hook-only (see Bootable), and it's still well before
 * init, where the schema registry collects definitions (init:1).
 */
final class TawData implements Bootable
{
    public function register(): void
    {
        add_action('after_setup_theme', [$this, 'bootData'], 0);
    }

    public function bootData(): void
    {
        Boot::data();
    }
}
