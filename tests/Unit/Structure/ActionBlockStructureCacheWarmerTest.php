<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Structure;

use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistry;
use PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlockStructureCacheWarmer;
use PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlockTemplateGenerator;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItem;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function is_file;
use function rmdir;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

class ActionBlockStructureCacheWarmerTest extends TestCase
{
    private string $cacheDir;
    private ActionBlockTemplateGenerator $templateGenerator;
    private ActionBlockStructureCacheWarmer $cacheWarmer;

    protected function setUp(): void
    {
        $this->cacheDir = sys_get_temp_dir() . '/' . uniqid('action-block-warmer-test', true);
        $this->templateGenerator = new ActionBlockTemplateGenerator(new ActionRegistry([new MockServiceActionItem()]));
        $this->cacheWarmer = new ActionBlockStructureCacheWarmer($this->templateGenerator);
    }

    protected function tearDown(): void
    {
        $templatePath = $this->cacheDir . ActionBlockStructureCacheWarmer::RELATIVE_TEMPLATE_PATH;
        if (is_file($templatePath) === true) {
            unlink($templatePath);
        }
        if (is_dir($this->cacheDir . '/perspeqtive_sulu_action_blocks/blocks') === true) {
            rmdir($this->cacheDir . '/perspeqtive_sulu_action_blocks/blocks');
        }
        if (is_dir($this->cacheDir . '/perspeqtive_sulu_action_blocks') === true) {
            rmdir($this->cacheDir . '/perspeqtive_sulu_action_blocks');
        }
        if (is_dir($this->cacheDir) === true) {
            rmdir($this->cacheDir);
        }
        if (is_file($this->cacheDir) === true) {
            unlink($this->cacheDir);
        }
    }

    public function testWritesGeneratedTemplateIntoCacheDirectory(): void
    {
        $this->cacheWarmer->warmUp($this->cacheDir);

        $templatePath = $this->cacheDir . ActionBlockStructureCacheWarmer::RELATIVE_TEMPLATE_PATH;

        self::assertFileExists($templatePath);
        self::assertSame($this->templateGenerator->generate(), file_get_contents($templatePath));
    }

    public function testOverwritesExistingTemplate(): void
    {
        $this->cacheWarmer->warmUp($this->cacheDir);
        $this->cacheWarmer->warmUp($this->cacheDir);

        $templatePath = $this->cacheDir . ActionBlockStructureCacheWarmer::RELATIVE_TEMPLATE_PATH;

        self::assertSame($this->templateGenerator->generate(), file_get_contents($templatePath));
    }

    public function testThrowsExceptionWhenDirectoryCannotBeCreated(): void
    {
        file_put_contents($this->cacheDir, 'blocking file');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not create directory');

        $this->cacheWarmer->warmUp($this->cacheDir);
    }

    public function testIsNotOptional(): void
    {
        self::assertFalse($this->cacheWarmer->isOptional());
    }
}
