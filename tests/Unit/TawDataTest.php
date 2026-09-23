<?php

declare(strict_types=1);

namespace TAW\Gutenberg\Tests\Unit;

use TAW\Core\Boot;
use TAW\Core\Rest\FieldMetaRegistrar;
use TAW\Core\Schema\Compiler;
use TAW\Gutenberg\Setup\TawData;
use TAW\Gutenberg\Tests\TestCase;

final class TawDataTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Boot::resetForTests();
    }

    protected function tearDown(): void
    {
        Boot::resetForTests();
        parent::tearDown();
    }

    public function test_register_defers_booting_to_after_setup_theme(): void
    {
        $service = new TawData();
        $service->register();

        $this->assertSame(0, has_action('after_setup_theme', [$service, 'bootData']));
        $this->assertFalse(Boot::isDataBooted());
    }

    public function test_boot_turns_on_the_data_layer_only(): void
    {
        (new TawData())->bootData();

        $this->assertTrue(Boot::isDataBooted());
        // Data layer: schema registry + REST field meta.
        $this->assertSame(1, has_action('init', [Compiler::class, 'collect']));
        $this->assertSame(20, has_action('init', [FieldMetaRegistrar::class, 'registerPostMeta']));
        // Nothing presentational: a block theme keeps its block CSS and gets
        // no Vite/Alpine/Performance output (taw/core ADR-0003).
        $this->assertFalse(has_action('wp_enqueue_scripts'));
        $this->assertFalse(has_action('wp_head'));
    }
}
