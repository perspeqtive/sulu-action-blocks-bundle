<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistryInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;

class MockActionRegistry implements ActionRegistryInterface
{
    public array $arrayResult = [];

    public function __construct(public ?ServiceActionItemInterface $result = new MockServiceActionItem())
    {
    }

    public function getActions(): iterable
    {
        if ($this->result === null) {
            return $this->arrayResult;
        }

        return [$this->result];
    }

    public function getAction(string $actionBlockIdentifier): ?ServiceActionItemInterface
    {
        return $this->result;
    }
}
