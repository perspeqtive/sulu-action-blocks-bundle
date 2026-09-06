<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionBlockExecutorInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionExecutionResult;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;

final class MockActionBlockExecutor implements ActionBlockExecutorInterface
{
    public ?ServiceActionItemInterface $actionItemToReturn = null;
    public ?string $foundActionBlockName = null;
    public ?ServiceActionItemInterface $executedActionItem = null;
    public ?array $executedOptions = null;

    public function __construct(
        public string $executionResult = '',
        public ActionExecutionResult $actionItemResult = new ActionExecutionResult(),
    ) {
    }

    public function execute(string $actionBlockName, array $options = []): string
    {
        return $this->executionResult . ' ' . $actionBlockName;
    }

    public function findActionItem(string $actionBlockName): ?ServiceActionItemInterface
    {
        $this->foundActionBlockName = $actionBlockName;

        return $this->actionItemToReturn;
    }

    public function executeActionItem(ServiceActionItemInterface $action, array $options = []): ActionExecutionResult
    {
        $this->executedActionItem = $action;
        $this->executedOptions = $options;

        return $this->actionItemResult;
    }
}
