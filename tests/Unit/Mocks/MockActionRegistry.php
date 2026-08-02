<?php

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistryInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;

class MockActionRegistry implements ActionRegistryInterface
{

    public function __construct(public ?ServiceActionItemInterface $result = new MockServiceActionItem()) {

    }

    /**
     * @inheritDoc
     */
    public function getActions(): iterable
    {
        if($this->result == null) {
            return [];
        }
        return [$this->result];
    }

    public function getAction(string $actionBlockIdentifier): ?ServiceActionItemInterface
    {
        return $this->result;
    }
}