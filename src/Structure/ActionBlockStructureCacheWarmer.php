<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Structure;

use PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlocks\ActionBlocksBuilderInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Structure\EmptyActionBlocks\EmptyActionBlocksBuilderInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

readonly class ActionBlockStructureCacheWarmer implements CacheWarmerInterface
{
    public function __construct(
        private ActionBlocksBuilderInterface $actionBlocksBuilder,
        private EmptyActionBlocksBuilderInterface $emptyActionBlockTemplatesGenerator,
    ) {
    }

    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        $this->emptyActionBlockTemplatesGenerator->build($cacheDir);
        $this->actionBlocksBuilder->build($cacheDir);

        return [];
    }

    public function isOptional(): bool
    {
        return false;
    }
}
