<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use Exception;
use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Configuration;
use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionExecutionResult;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;

final class MockServiceActionItemWithException implements ServiceActionItemInterface
{
    public function getIdentifier(): string
    {
        return self::class;
    }

    public function getTitle(): string
    {
        return 'Exception Title';
    }

    public function getConfigurationBlock(): ?string
    {
        return 'exception-configuration-block';
    }

    public function execute(array $options = []): ActionExecutionResult
    {
        throw new Exception('Action Exception');
    }
}
