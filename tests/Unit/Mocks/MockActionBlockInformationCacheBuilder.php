<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationCacheBuilderInterface;

class MockActionBlockInformationCacheBuilder implements ActionBlockInformationCacheBuilderInterface
{
    public bool $wasBuilt = false;

    public function build(string $cacheDir): void
    {
        $this->wasBuilt = true;
    }
}
