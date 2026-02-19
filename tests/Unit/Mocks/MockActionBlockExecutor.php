<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionBlockExecutorInterface;

class MockActionBlockExecutor implements ActionBlockExecutorInterface
{
    public function __construct(public string $executionResult = '')
    {
    }

    public function execute(int $actionBlockIdentifier, array $options = []): string
    {
        return $this->executionResult . ' ' . $actionBlockIdentifier;
    }
}
