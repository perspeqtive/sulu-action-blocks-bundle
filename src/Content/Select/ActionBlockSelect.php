<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Content\Select;

use PERSPEQTIVE\SuluActionBlockBundle\Repository\ActionBlockRepository;

readonly class ActionBlockSelect
{

    public function __construct(private ActionBlockRepository $actionBlockRepository) {

    }

    public function getValues(): array {
        $actionBlocks = $this->actionBlockRepository->findAll();

        $values = [0 => 'Bitte auswählen'];

        foreach ($actionBlocks as $actionBlock) {
            $values[$actionBlock->getId()] = $actionBlock->getTitle();
        }

        return $values;
    }

}