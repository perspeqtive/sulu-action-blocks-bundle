<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\InformationMap;

use RuntimeException;

use function file_exists;
use function file_get_contents;
use function json_decode;

readonly class ActionBlockInformationProviderFactory
{
    public function __construct(private string $cacheFilePath)
    {
    }

    public function create(): ActionBlockInformationProvider
    {
        $filepath = $this->cacheFilePath . ActionBlockInformationCacheBuilder::CACHE_FILE_NAME;
        if (!file_exists($filepath)) {
            throw new RuntimeException('Cache file does not exist');
        }
        $contents = file_get_contents($filepath);
        $data = json_decode($contents, true);
        if ($data === null) {
            throw new RuntimeException('Cache file is not valid JSON');
        }

        return new ActionBlockInformationProvider($data);
    }
}
