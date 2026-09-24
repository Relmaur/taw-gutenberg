<?php

declare(strict_types=1);

namespace TAW\Gutenberg\Tests\Unit;

use TAW\Core\Boot;
use TAW\Core\Editing\Editing as CoreEditing;
use TAW\Core\Editing\Resolver;
use TAW\Core\Schema\JsonLoader;
use TAW\Gutenberg\Setup\Editing;
use TAW\Gutenberg\Tests\TestCase;

final class EditingTest extends TestCase
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
        $service = new Editing();
        $service->register();

        $this->assertSame(0, has_action('after_setup_theme', [$service, 'bootEditing']));
        $this->assertFalse(Boot::isEditingBooted());
    }

    public function test_boot_turns_on_editing_policies_and_the_data_layer(): void
    {
        (new Editing())->bootEditing();

        $this->assertTrue(Boot::isEditingBooted());
        $this->assertTrue(Boot::isDataBooted());
        $this->assertSame(CoreEditing::APPLY_PRIORITY, has_action('init', [CoreEditing::class, 'apply']));
    }

    public function test_the_shipped_policy_is_open(): void
    {
        // A fresh install must be unrestricted; clients opt into a level with
        // TAW_EDITING_PRESET (ADR-0002).
        $read = JsonLoader::readFile(\dirname(__DIR__, 2) . '/taw-schema/editing.json');
        $this->assertSame([], $read['errors']);

        $policy = Resolver::resolve(JsonLoader::toDefinition($read['data']));

        $this->assertSame('open', $policy->preset);
        $this->assertNotContains(false, $policy->site);
        $this->assertNotContains(false, $policy->design);
        $this->assertNotContains(false, $policy->features);
        $this->assertFalse($policy->content('page')['lock']);
        $this->assertNull($policy->content('page')['allow']);
    }
}
