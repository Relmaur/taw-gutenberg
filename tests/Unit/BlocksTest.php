<?php

declare(strict_types=1);

namespace TAW\Gutenberg\Tests\Unit;

use Brain\Monkey\Functions;
use TAW\Core\Assets\Vite;
use TAW\Gutenberg\Setup\Blocks;
use TAW\Gutenberg\Tests\TestCase;

final class BlocksTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        parent::setUp();
        Vite::resetForTests();
        $this->root = sys_get_temp_dir() . '/taw-gutenberg-blocks-' . getmypid() . '-' . bin2hex(random_bytes(3));
        foreach (['alpha', 'beta', 'no-json'] as $block) {
            mkdir("{$this->root}/src/blocks/{$block}", 0777, true);
        }
        file_put_contents("{$this->root}/src/blocks/alpha/block.json", '{"name":"taw-gutenberg/alpha"}');
        file_put_contents("{$this->root}/src/blocks/beta/block.json", '{"name":"taw-gutenberg/beta"}');
    }

    protected function tearDown(): void
    {
        exec('rm -rf ' . escapeshellarg($this->root));
        Vite::resetForTests();
        parent::tearDown();
    }

    public function test_registers_on_init_only(): void
    {
        $blocks = new Blocks();
        $blocks->register();

        $this->assertSame(10, has_action('init', [$blocks, 'registerBlocks']));
    }

    public function test_finds_every_folder_with_a_block_json(): void
    {
        $this->assertSame(
            ["{$this->root}/src/blocks/alpha", "{$this->root}/src/blocks/beta"],
            (new Blocks())->blockDirs($this->root)
        );
        $this->assertSame([], (new Blocks())->blockDirs($this->root . '/missing'));
    }

    public function test_registers_each_block_through_vite(): void
    {
        Functions\when('wp_normalize_path')->returnArg();
        $registered = [];
        Functions\when('register_block_type')->alias(static function (string $dir) use (&$registered): bool {
            $registered[] = basename($dir);

            return false;
        });

        (new Blocks(new Vite($this->root, 'https://site.test/theme')))->registerBlocks();

        $this->assertSame(['alpha', 'beta'], $registered);
    }

    public function test_the_shipped_example_block_is_found(): void
    {
        $this->assertContains(dirname(__DIR__, 2) . '/src/blocks/callout', (new Blocks())->blockDirs(dirname(__DIR__, 2)));
    }
}
