<?php

declare(strict_types=1);

namespace TAW\Gutenberg\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TAW\Core\Schema\JsonLoader;
use TAW\Core\Schema\Source;

/**
 * The theme's own taw-schema/ must always be valid — an invalid file is
 * silently skipped at runtime (with only a debug notice), so catch it here.
 */
final class SchemaFilesTest extends TestCase
{
    public function test_every_schema_file_is_valid(): void
    {
        $files = JsonLoader::findFiles([\dirname(__DIR__, 2) . '/taw-schema' => Source::RANK_PARENT_THEME]);
        $this->assertNotSame([], $files);

        foreach ($files as $file) {
            $this->assertSame([], JsonLoader::readFile($file['path'])['errors'], $file['path']);
        }
    }
}
