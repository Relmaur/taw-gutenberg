<?php
/**
 * TAW Gutenberg — theme bootstrap.
 *
 * Everything visual lives in theme.json, templates/ and parts/ (plain
 * Gutenberg). This file only loads Composer and starts the theme's services;
 * the data layer (post types, fields, options) comes from taw/core — see
 * app/Setup/TawData.php and taw-schema/.
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$tawGutenbergAutoload = __DIR__ . '/vendor/autoload.php';

// A theme activated before `composer install` ran would otherwise fatal on
// the first request. Fail soft: the site still renders its templates (they
// need no PHP), and administrators see what to run.
if (!is_readable($tawGutenbergAutoload)) {
    add_action('admin_notices', static function (): void {
        if (current_user_can('switch_themes')) {
            echo '<div class="notice notice-error"><p><strong>TAW Gutenberg:</strong> run <code>composer install</code> in the theme folder — taw/core (the data layer) is not installed.</p></div>';
        }
    });

    return;
}

require_once $tawGutenbergAutoload;

(new \TAW\Gutenberg\Theme())->register();
