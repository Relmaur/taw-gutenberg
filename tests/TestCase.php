<?php

declare(strict_types=1);

namespace TAW\Gutenberg\Tests;

use Brain\Monkey;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void
    {
        // Brain Monkey expectations are Mockery assertions checked at teardown.
        $this->addToAssertionCount(\Mockery::getContainer()->mockery_getExpectationCount());
        Monkey\tearDown();
        parent::tearDown();
    }
}
