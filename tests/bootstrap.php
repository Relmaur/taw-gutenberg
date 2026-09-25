<?php

declare(strict_types=1);

// Unit tests run WITHOUT WordPress; Brain Monkey stubs WP functions per test.
// taw/core's classes guard themselves with `if (!defined('ABSPATH')) exit;`.
if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/');
}

// WordPress time constants taw/core reads (e.g. Assets\Vite's manifest cache TTL).
if (!defined('DAY_IN_SECONDS')) {
    define('DAY_IN_SECONDS', 86400);
}

require __DIR__ . '/../vendor/autoload.php';
