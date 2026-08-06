<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\InformationMap;

interface ActionBlockInformationCacheBuilderInterface
{
    public function build(string $cacheDir): void;
}
