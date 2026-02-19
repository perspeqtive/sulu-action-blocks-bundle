<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Content\Select;

use PERSPEQTIVE\SuluActionBlocksBundle\Content\Select\ActionSelect;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistry;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItem;
use PHPUnit\Framework\TestCase;

class ActionSelectTest extends TestCase
{
    public function testGetValues(): void
    {
        $registry = new ActionRegistry([new MockServiceActionItem()]);
        $select = new ActionSelect($registry);

        $values = $select->getValues();

        self::assertCount(1, $values);
        self::assertEquals(
            [[
                'name' => MockServiceActionItem::class,
                'title' => 'Hello World Title']],
            $values,
        );
    }
}
