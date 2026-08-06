<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\InformationMap;

interface ActionBlockInformationProviderInterface
{
    public function provide(): ActionBlockInformationCollection;
}
