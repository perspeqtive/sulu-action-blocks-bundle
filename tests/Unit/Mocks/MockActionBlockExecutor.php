<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlockBundle\Execution\ActionBlockExecutorInterface;

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
