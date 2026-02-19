<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Tests\Unit\Content\Select;

use PERSPEQTIVE\SuluActionBlockBundle\Content\Select\ActionBlockSelect;
use PERSPEQTIVE\SuluActionBlockBundle\Entity\ActionBlock;
use PERSPEQTIVE\SuluActionBlockBundle\Tests\Unit\Mocks\MockActionBlockRepository;
use PHPUnit\Framework\TestCase;

class ActionBlockSelectTest extends TestCase
{
    private MockActionBlockRepository $repository;
    private ActionBlockSelect $select;

    protected function setUp(): void
    {
        $this->repository = new MockActionBlockRepository();
        $this->select = new ActionBlockSelect($this->repository);
    }

    public function testGetValues(): void
    {
        $actionBlock1 = new ActionBlock();
        $actionBlock1->setId(1);
        $actionBlock1->setTitle('Block 1');

        $actionBlock2 = new ActionBlock();
        $actionBlock2->setId(2);
        $actionBlock2->setTitle('Block 2');

        $this->repository->findAllResult = [$actionBlock1, $actionBlock2];

        $values = $this->select->getValues();

        self::assertCount(2, $values);
        self::assertEquals(['name' => 1, 'title' => 'Block 1'], $values[0]);
        self::assertEquals(['name' => 2, 'title' => 'Block 2'], $values[1]);
    }
}
