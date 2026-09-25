<?php

declare(strict_types=1);

namespace TAW\Gutenberg\Tests\Unit;

use TAW\Gutenberg\Contracts\Bootable;
use TAW\Gutenberg\Setup\Assets;
use TAW\Gutenberg\Setup\Blocks;
use TAW\Gutenberg\Setup\Editing;
use TAW\Gutenberg\Setup\TawData;
use TAW\Gutenberg\Tests\TestCase;
use TAW\Gutenberg\Theme;

final class ThemeTest extends TestCase
{
    public function test_every_service_is_bootable_and_each_is_registered_once(): void
    {
        $services = (new Theme())->services();

        $this->assertContainsOnlyInstancesOf(Bootable::class, $services);
        $this->assertCount(1, array_filter($services, static fn (Bootable $s): bool => $s instanceof TawData));
        $this->assertCount(1, array_filter($services, static fn (Bootable $s): bool => $s instanceof Editing));
        $this->assertCount(1, array_filter($services, static fn (Bootable $s): bool => $s instanceof Blocks));
        $this->assertCount(1, array_filter($services, static fn (Bootable $s): bool => $s instanceof Assets));
    }

    public function test_registering_the_theme_only_adds_hooks(): void
    {
        // The Bootable contract: nothing runs until WordPress fires the hook.
        (new Theme())->register();

        $this->assertTrue(has_action('after_setup_theme'));
        $this->assertFalse(\TAW\Core\Boot::isDataBooted());
        $this->assertFalse(\TAW\Core\Boot::isEditingBooted());
    }
}
