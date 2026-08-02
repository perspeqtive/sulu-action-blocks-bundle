<?php

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Structure\EmptyActionBlocks\EmptyActionBlocksBuilderInterface;

class MockEmptyActionBlocksBuilder implements EmptyActionBlocksBuilderInterface
{

    public bool $wasBuilt = false;

    public function build(string $cacheDir): void
    {
        $this->wasBuilt = true;
    }
}