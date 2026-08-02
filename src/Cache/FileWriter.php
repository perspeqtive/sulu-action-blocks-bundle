<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Cache;

use RuntimeException;

use function file_put_contents;
use function is_dir;
use function mkdir;
use function sprintf;

readonly class FileWriter
{
    public function __construct(private string $cacheSubDir)
    {
    }

    public function writeContent(string $content, string $cacheDir, string $fileName): void
    {
        $fileDir = $cacheDir . $this->cacheSubDir;
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
