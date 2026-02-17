<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Execution;

use PERSPEQTIVE\SuluActionBlockBundle\Registry\ActionRegistry;

readonly class ActionBlockExecutor
{

    public function __construct(private ActionRegistry $actionRegistry) {

    }

    public function execute(string $actionBlockIdentifier, array $configuration = []): string
    {
        $action = $this->actionRegistry->getAction($actionBlockIdentifier);
        return $action->execute($configuration);
    }

}