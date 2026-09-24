<?php

declare(strict_types=1);

namespace TAW\Gutenberg;

use TAW\Gutenberg\Contracts\Bootable;
use TAW\Gutenberg\Setup\Editing;
use TAW\Gutenberg\Setup\TawData;

/**
 * The theme's service registry — the one list of everything the theme wires
 * into WordPress. Add a service by adding its class to services().
 */
final class Theme
{
    /**
     * @return list<Bootable>
     */
    public function services(): array
    {
        return [
            new TawData(),
            new Editing(),
        ];
    }

    public function register(): void
    {
        foreach ($this->services() as $service) {
            $service->register();
        }
    }
}
