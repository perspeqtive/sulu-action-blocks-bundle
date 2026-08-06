<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\InformationMap;

use PERSPEQTIVE\SuluActionBlocksBundle\Cache\CacheFileWriterInterface;

use function json_encode;

readonly class ActionBlockInformationCacheBuilder implements ActionBlockInformationCacheBuilderInterface
{
    public const CACHE_FILE_NAME = 'action_block_information.json';

    public function __construct(
        private ActionBlockInformationBuilder $actionBlockInformationBuilder,
        private CacheFileWriterInterface $cacheFileWriter,
    ) {
    }

    public function build(string $cacheDir): void
    {
        $information = $this->actionBlockInformationBuilder->provide();

        $this->cacheFileWriter->writeContent(json_encode($information), $cacheDir, self::CACHE_FILE_NAME);
    }
}
