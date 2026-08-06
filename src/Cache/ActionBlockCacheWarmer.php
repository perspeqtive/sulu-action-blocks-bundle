<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Cache;

use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationCacheBuilderInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlocks\ActionBlocksBuilderInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Structure\EmptyActionBlocks\EmptyActionBlocksBuilderInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

readonly class ActionBlockCacheWarmer implements CacheWarmerInterface
{
    public function __construct(
        private ActionBlocksBuilderInterface $actionBlocksBuilder,
        private EmptyActionBlocksBuilderInterface $emptyActionBlockTemplatesGenerator,
        private ActionBlockInformationCacheBuilderInterface $actionBlockInformationCacheBuilder,
    ) {
    }

    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        $this->actionBlockInformationCacheBuilder->build($cacheDir);
        $this->emptyActionBlockTemplatesGenerator->build($cacheDir);
        $this->actionBlocksBuilder->build($cacheDir);

        return [];
    }

    public function isOptional(): bool
    {
        return false;
    }
}
