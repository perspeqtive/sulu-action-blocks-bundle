<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Cache;

use RuntimeException;

use function file_put_contents;
use function is_dir;
use function mkdir;
use function sprintf;

readonly class FileWriter implements CacheFileWriterInterface
{
    public function __construct(private string $cacheSubDir)
    {
    }

    public function writeBlockContent(string $content, string $cacheDir, string $fileName): void
    {
        $fileDir = $cacheDir . $this->cacheSubDir . '/blocks';
        $this->write($fileDir, $fileName, $content);
    }

    public function writeContent(string $content, string $cacheDir, string $fileName): void
    {
        $fileDir = $cacheDir . $this->cacheSubDir;
        $this->write($fileDir, $fileName, $content);
    }

    private function write(string $fileDir, string $fileName, string $content): void
    {
        $filePath = $fileDir . '/' . $fileName;

        $this->ensureDirectory($fileDir);

        if (@file_put_contents($filePath, $content) === false) {
            throw new RuntimeException(sprintf('Could not write generated action block template "%s".', $filePath));
        }
    }

    private function ensureDirectory(string $fileDir): void
    {
        if (is_dir($fileDir) === false && @mkdir($fileDir, 0777, true) === false) {
            throw new RuntimeException(sprintf('Could not create directory "%s".', $fileDir));
        }
    }
}
