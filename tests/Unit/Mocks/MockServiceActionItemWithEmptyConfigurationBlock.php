<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionExecutionResult;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;

class MockServiceActionItemWithEmptyConfigurationBlock implements ServiceActionItemInterface
{
    public function getIdentifier(): string
    {
        return self::class;
    }

    public function getTitle(): string
    {
        return 'Empty Configuration Block Title';
    }

    public function getConfigurationBlock(): ?string
    {
        return '';
    }

    public function execute(array $options = []): ActionExecutionResult
    {
        return new ActionExecutionResult();
    }
}
