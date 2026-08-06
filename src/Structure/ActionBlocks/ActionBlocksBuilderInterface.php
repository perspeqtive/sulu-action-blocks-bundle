<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlocks;

interface ActionBlocksBuilderInterface
{
    public function build(string $cacheDir): void;
}
