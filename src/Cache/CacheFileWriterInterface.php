<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Cache;

interface CacheFileWriterInterface
{
    public function writeContent(string $content, string $cacheDir, string $fileName): void;
}
