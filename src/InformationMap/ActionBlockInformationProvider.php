<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\InformationMap;

readonly class ActionBlockInformationProvider implements ActionBlockInformationProviderInterface
{
    public function __construct(
        private array $data = [],
    ) {
    }

    public function provide(): ActionBlockInformationCollection
    {
        return ActionBlockInformationCollection::fromArray($this->data);
    }
}
