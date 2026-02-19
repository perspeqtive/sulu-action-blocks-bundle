<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Content\Select;

use PERSPEQTIVE\SuluActionBlocksBundle\Repository\ActionBlockRepositoryInterface;

readonly class ActionBlockSelect
{
    public function __construct(private ActionBlockRepositoryInterface $actionBlockRepository)
    {
    }

    public function getValues(): array
    {
        $actionBlocks = $this->actionBlockRepository->findAll();

        $values = [];
        foreach ($actionBlocks as $actionBlock) {
            $values[] = [
                'name' => $actionBlock->getId(),
                'title' => $actionBlock->getTitle(),
            ];
        }

        return $values;
    }
}
