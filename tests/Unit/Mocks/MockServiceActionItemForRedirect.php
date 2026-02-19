<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionExecutionResult;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;

class MockServiceActionItemForRedirect implements ServiceActionItemInterface
{
    public function getIdentifier(): string
    {
        return self::class;
    }

    public function getTitle(): string
    {
        return 'Redirect Item';
    }

    public function execute(array $configuration = [], array $options = []): ActionExecutionResult
    {
        return new ActionExecutionResult('', $configuration['redirect'] ?? '');
    }
}
