<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlockBundle\Execution\ActionExecutionResult;
use PERSPEQTIVE\SuluActionBlockBundle\Registry\ServiceActionItemInterface;

class MockServiceActionItem implements ServiceActionItemInterface
{
    public function getIdentifier(): string
    {
        return self::class;
    }

    public function getTitle(): string
    {
        return 'Hello World Title';
    }

    public function execute(array $configuration = [], array $options = []): ActionExecutionResult
    {
        return new ActionExecutionResult('<h1>Hello</h1>');
    }
}
