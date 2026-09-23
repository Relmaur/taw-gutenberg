<?php

declare(strict_types=1);

// Unit tests run WITHOUT WordPress; Brain Monkey stubs WP functions per test.
// taw/core's classes guard themselves with `if (!defined('ABSPATH')) exit;`.
if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/');
}

require __DIR__ . '/../vendor/autoload.php';
