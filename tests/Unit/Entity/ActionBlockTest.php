<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Entity;

use PERSPEQTIVE\SuluActionBlocksBundle\Entity\ActionBlock;
use PHPUnit\Framework\TestCase;

class ActionBlockTest extends TestCase
{
    public function testToArray(): void
    {
        $entity = new ActionBlock();
        $entity->setTitle('Test');
        $entity->setAction('action');
        $entity->setConfiguration(['a' => 'b']);

        $expected = [
            'id' => null,
            'title' => 'Test',
            'action' => 'action',
            'configuration' => ['a' => 'b'],
        ];

        self::assertEquals($expected, $entity->toArray());
    }
}
