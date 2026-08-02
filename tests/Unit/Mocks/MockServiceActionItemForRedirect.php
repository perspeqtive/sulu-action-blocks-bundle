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

    public function getConfigurationBlock(): string
    {
        return 'redirect-configuration-block';
    }

    public function execute(array $options = []): ActionExecutionResult
    {
        return new ActionExecutionResult('', $options['redirect'] ?? '');
    }
}
