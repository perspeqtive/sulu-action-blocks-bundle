<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

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

    public function getConfigurationBlock(): ?string
    {
        return 'mock-configuration-block';
    }

    public function execute(array $options = []): ActionExecutionResult
    {
        return new ActionExecutionResult('<h1>Hello</h1>');
    }
}
