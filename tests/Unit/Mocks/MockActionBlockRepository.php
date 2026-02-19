<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Entity\ActionBlock;
use PERSPEQTIVE\SuluActionBlocksBundle\Repository\ActionBlockRepositoryInterface;

class MockActionBlockRepository implements ActionBlockRepositoryInterface
{
    /** @var ActionBlock[] */
    public array $findAllResult = [];

    public function __construct(public ?ActionBlock $findResult = null)
    {
    }

    public function findById(int $id): ?ActionBlock
    {
        return $this->findResult;
    }

    public function findAll(): array
    {
        return $this->findAllResult;
    }
}
