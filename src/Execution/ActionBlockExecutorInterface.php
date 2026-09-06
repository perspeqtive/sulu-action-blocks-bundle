<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Execution;

use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;

interface ActionBlockExecutorInterface
{
    public function execute(string $actionBlockName, array $options = []): string;

    public function findActionItem(string $actionBlockName): ?ServiceActionItemInterface;

    public function executeActionItem(ServiceActionItemInterface $action, array $options = []): ActionExecutionResult;
}
