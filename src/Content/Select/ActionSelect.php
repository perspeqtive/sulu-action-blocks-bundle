<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Content\Select;

use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistry;

readonly class ActionSelect
{
    public function __construct(private ActionRegistry $actionRegistry)
    {
    }

    public function getValues(): array
    {
        $result = [];
        foreach ($this->actionRegistry->getActions() as $action) {
            $result[] = [
                'name' => $action->getIdentifier(),
                'title' => $action->getTitle(),
            ];
        }

        return $result;
    }
}
