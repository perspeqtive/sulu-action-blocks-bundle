<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\InformationMap;

use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationCacheBuilder;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationProvider;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationProviderFactory;
use PHPUnit\Framework\TestCase;
use RuntimeException;

use function bin2hex;
use function file_exists;
use function file_put_contents;
use function mkdir;
use function random_bytes;
use function rmdir;
use function sys_get_temp_dir;
use function unlink;

final class ActionBlockInformationProviderFactoryTest extends TestCase
{
    private string $cacheDirectory;
    private ActionBlockInformationProviderFactory $factory;

    protected function setUp(): void
    {
        $this->cacheDirectory = sys_get_temp_dir() . '/action-block-information-' . bin2hex(random_bytes(8)) . '/';
        mkdir($this->cacheDirectory);

        $this->factory = new ActionBlockInformationProviderFactory($this->cacheDirectory);
    }

    public function testCreateThrowsExceptionWhenCacheFileDoesNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cache file does not exist');

        $this->factory->create();
    }

    public function testCreateThrowsExceptionWhenCacheFileContainsInvalidJson(): void
    {
        $this->writeCacheFile('{invalid json');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cache file is not valid JSON');

        $this->factory->create();
    }

    public function testCreateReturnsProviderWithInformationFromCacheFile(): void
    {
        $this->writeCacheFile('[{"blockName":"contact-form","title":"Contact form",'
            . '"identifier":"App\\\\Action\\\\ContactFormAction","needsGeneration":true}]');

        $provider = $this->factory->create();

        self::assertInstanceOf(ActionBlockInformationProvider::class, $provider);
        $information = $provider->provide()->first();
        self::assertNotNull($information);
        self::assertSame('contact-form', $information->blockName);
        self::assertSame('Contact form', $information->title);
        self::assertSame('App\\Action\\ContactFormAction', $information->identifier);
        self::assertTrue($information->needsGeneration);
    }

    protected function tearDown(): void
    {
        $cacheFile = $this->cacheDirectory . ActionBlockInformationCacheBuilder::CACHE_FILE_NAME;
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        rmdir($this->cacheDirectory);
    }

    private function writeCacheFile(string $contents): void
    {
        file_put_contents(
            $this->cacheDirectory . ActionBlockInformationCacheBuilder::CACHE_FILE_NAME,
            $contents,
        );
    }
}
