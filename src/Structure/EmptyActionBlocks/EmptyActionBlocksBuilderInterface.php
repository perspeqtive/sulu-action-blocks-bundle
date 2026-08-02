<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Structure\EmptyActionBlocks;

interface EmptyActionBlocksBuilderInterface
{
    public function build(string $cacheDir): void;
}
