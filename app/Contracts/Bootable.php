<?php

declare(strict_types=1);

namespace TAW\Gutenberg\Contracts;

/**
 * A theme service.
 *
 * Rule for every implementation: register() ONLY adds WordPress hooks — it
 * never does the work itself. That keeps construction side-effect free, so
 * the whole service list can be built in a unit test without WordPress, and
 * every piece of real work happens at a named, testable hook.
 */
interface Bootable
{
    public function register(): void;
}
