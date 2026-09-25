<?php

declare(strict_types=1);

namespace TAW\Gutenberg\Setup;

use TAW\Core\Assets\Vite;
use TAW\Gutenberg\Contracts\Bootable;

/**
 * Site-wide scripts and styles, built by Vite (ADR-0003).
 *
 * Presentation lives in theme.json, so the theme ships none yet: an empty
 * entry would cost every page a request. To add one, list it here AND in
 * GLOBAL_ENTRIES in vite.config.js, e.g.
 *
 *   public const FRONTEND = ['taw-gutenberg-main' => 'src/js/main.ts'];
 *
 * Each entry is an ES module; CSS it imports is enqueued with it.
 */
final class Assets implements Bootable
{
    /** @var array<string, string> Handle → source, on the front end. */
    public const FRONTEND = [];

    /** @var array<string, string> Handle → source, in the block editor. */
    public const EDITOR = [];

    /**
     * @param array<string, string>|null $frontend
     * @param array<string, string>|null $editor
     */
    public function __construct(
        private ?Vite $vite = null,
        private ?array $frontend = null,
        private ?array $editor = null,
    ) {
    }

    public function register(): void
    {
        if ($this->frontend() !== []) {
            add_action('wp_enqueue_scripts', [$this, 'enqueueFrontend']);
        }
        if ($this->editor() !== []) {
            add_action('enqueue_block_editor_assets', [$this, 'enqueueEditor']);
        }
    }

    public function enqueueFrontend(): void
    {
        $this->enqueue($this->frontend());
    }

    public function enqueueEditor(): void
    {
        $this->enqueue($this->editor());
    }

    /**
     * @param array<string, string> $entries
     */
    private function enqueue(array $entries): void
    {
        $vite = $this->vite ??= Vite::theme();

        foreach ($entries as $handle => $source) {
            $vite->script($handle, $source);
        }
    }

    /**
     * @return array<string, string>
     */
    private function frontend(): array
    {
        return $this->frontend ?? self::FRONTEND;
    }

    /**
     * @return array<string, string>
     */
    private function editor(): array
    {
        return $this->editor ?? self::EDITOR;
    }
}
