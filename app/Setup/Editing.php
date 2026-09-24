<?php

declare(strict_types=1);

namespace TAW\Gutenberg\Setup;

use TAW\Core\Boot;
use TAW\Gutenberg\Contracts\Bootable;

/**
 * Turns on taw/core's editing policies (taw/core ADR-0005, this theme's
 * ADR-0002): how far the block editor is locked down, layer by layer.
 *
 * The policy itself lives in taw-schema/editing.json ("preset": "open", so
 * a fresh install is unrestricted). A client install picks its level in
 * wp-config.php:
 *
 *   define('TAW_EDITING_PRESET', 'structured');       // open | guided | structured | locked
 *   define('TAW_EDITING_BYPASS_USERS', ['marco']);    // logins that stay unlocked
 *   define('TAW_EDITING_OFF', true);                  // recovery: turn it all off
 *
 * Booted at after_setup_theme:0 like TawData: register() stays hook-only, and
 * it's before init, where the policy is resolved (init:7).
 */
final class Editing implements Bootable
{
    public function register(): void
    {
        add_action('after_setup_theme', [$this, 'bootEditing'], 0);
    }

    public function bootEditing(): void
    {
        Boot::editing();
    }
}
