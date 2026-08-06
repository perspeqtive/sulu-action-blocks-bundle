<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Cache\CacheFileWriterInterface;

final class MockCacheFileWriter implements CacheFileWriterInterface
{
    public string $content = '';
    public array $cacheDirectories = [];
    public array $fileNames = [];

    public function writeContent(string $content, string $cacheDir, string $fileName): void
    {
        $this->content = $content;
        $this->cacheDirectories[] = $cacheDir;
        $this->fileNames[] = $fileName;
    }

    public function writeBlockContent(string $content, string $cacheDir, string $fileName): void
    {
        $this->content = $content;
        $this->cacheDirectories[] = $cacheDir;
        $this->fileNames[] = $fileName;
    }
}
