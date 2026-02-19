<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Tests\Unit\Content\Select;

use PERSPEQTIVE\SuluActionBlockBundle\Content\Select\ActionSelect;
use PERSPEQTIVE\SuluActionBlockBundle\Registry\ActionRegistry;
use PERSPEQTIVE\SuluActionBlockBundle\Tests\Unit\Mocks\MockServiceActionItem;
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
