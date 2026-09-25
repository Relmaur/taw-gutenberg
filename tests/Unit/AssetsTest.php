<?php

declare(strict_types=1);

namespace TAW\Gutenberg\Tests\Unit;

use Brain\Monkey\Functions;
use TAW\Core\Assets\Vite;
use TAW\Gutenberg\Setup\Assets;
use TAW\Gutenberg\Tests\TestCase;

final class AssetsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Vite::resetForTests();
    }

    public function test_ships_no_global_assets_so_pages_pay_for_none(): void
    {
        $this->assertSame([], Assets::FRONTEND);
        $this->assertSame([], Assets::EDITOR);

        $assets = new Assets();
        $assets->register();

        $this->assertFalse(has_action('wp_enqueue_scripts', [$assets, 'enqueueFrontend']));
        $this->assertFalse(has_action('enqueue_block_editor_assets', [$assets, 'enqueueEditor']));
    }

    public function test_listed_entries_are_hooked_and_enqueued_through_vite(): void
    {
        $root = sys_get_temp_dir() . '/taw-gutenberg-assets-' . getmypid();
        @mkdir($root . '/dist/.vite', 0777, true);
        file_put_contents($root . '/dist/.vite/manifest.json', (string) json_encode([
            'src/js/main.ts'   => ['file' => 'main-1.js'],
            'src/js/editor.ts' => ['file' => 'editor-1.js'],
        ]));
        Functions\when('wp_cache_get')->justReturn(false);
        Functions\when('wp_cache_set')->justReturn(true);
        $enqueued = [];
        Functions\when('wp_register_script')->alias(static function (string $handle, string $src) use (&$enqueued): bool {
            $enqueued[$handle] = $src;

            return true;
        });
        Functions\when('wp_enqueue_script')->justReturn(null);

        try {
            $assets = new Assets(new Vite($root, 'https://site.test/theme'), ['acme-main' => 'src/js/main.ts'], ['acme-editor' => 'src/js/editor.ts']);
            $assets->register();

            $this->assertSame(10, has_action('wp_enqueue_scripts', [$assets, 'enqueueFrontend']));
            $this->assertSame(10, has_action('enqueue_block_editor_assets', [$assets, 'enqueueEditor']));

            $assets->enqueueFrontend();
            $assets->enqueueEditor();

            $this->assertSame([
                'acme-main'   => 'https://site.test/theme/dist/main-1.js',
                'acme-editor' => 'https://site.test/theme/dist/editor-1.js',
            ], $enqueued);
        } finally {
            exec('rm -rf ' . escapeshellarg($root));
        }
    }
}
