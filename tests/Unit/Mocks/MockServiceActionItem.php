<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Configuration;
use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionExecutionResult;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;

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

    public function execute(Configuration $configuration, array $options = []): ActionExecutionResult
    {
        return new ActionExecutionResult('<h1>Hello</h1>');
    }
}
