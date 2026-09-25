<?php

declare(strict_types=1);

namespace TAW\Gutenberg\Setup;

use TAW\Core\Assets\Vite;
use TAW\Gutenberg\Contracts\Bootable;

/**
 * Registers every block in src/blocks/<name>/block.json (ADR-0003).
 *
 * taw/core's Assets\Vite::block() loads each block's `file:` assets
 * (index.tsx, style.scss…) from the Vite dev server or the build in dist/.
 * A block that isn't built still registers; its missing assets are named in
 * an admin notice.
 */
final class Blocks implements Bootable
{
    /**
     * WordPress script handles block editor scripts need beyond taw/core's
     * Vite::EDITOR_SCRIPT_DEPS (blocks, block-editor, components, element,
     * i18n, data). Add one when a block imports another @wordpress package,
     * e.g. 'wp-rich-text' for @wordpress/rich-text.
     *
     * @var list<string>
     */
    public const EXTRA_EDITOR_DEPS = [];

    public function __construct(private ?Vite $vite = null)
    {
    }

    public function register(): void
    {
        add_action('init', [$this, 'registerBlocks']);
    }

    public function registerBlocks(): void
    {
        $vite = $this->vite ??= Vite::theme();

        $deps = [...Vite::EDITOR_SCRIPT_DEPS, ...self::EXTRA_EDITOR_DEPS];

        foreach ($this->blockDirs($vite->dir()) as $dir) {
            $vite->block($dir, [], $deps);
        }
    }

    /**
     * @return list<string>
     */
    public function blockDirs(string $themeDir): array
    {
        $files = glob($themeDir . '/src/blocks/*/block.json');

        return array_map('dirname', $files === false ? [] : $files);
    }
}
