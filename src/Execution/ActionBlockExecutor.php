<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Execution;

use PERSPEQTIVE\SuluActionBlockBundle\Registry\ActionRegistry;
use PERSPEQTIVE\SuluActionBlockBundle\Registry\ServiceActionItemInterface;
use PERSPEQTIVE\SuluActionBlockBundle\Repository\ActionBlockRepository;

readonly class ActionBlockExecutor
{
    public function __construct(
        private ActionBlockRepository $actionBlockRepository,
        private ActionRegistry $actionRegistry
    )
    {
    }

    public function execute(int $actionBlockIdentifier, array $options = []): string
    {
        $actionBlock = $this->actionBlockRepository->findById($actionBlockIdentifier);

        $action = $this->actionRegistry->getAction($actionBlock->getAction());
        if ($action instanceof ServiceActionItemInterface === false) {
            return '';
        }

        return $action->execute($actionBlock->getConfiguration(), $options);
    }
}
