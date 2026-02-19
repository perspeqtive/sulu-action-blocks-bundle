<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlockBundle\Entity\ActionBlock;
use PERSPEQTIVE\SuluActionBlockBundle\Repository\ActionBlockRepositoryInterface;

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
