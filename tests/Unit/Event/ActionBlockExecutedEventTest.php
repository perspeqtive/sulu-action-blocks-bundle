<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Event;

use PERSPEQTIVE\SuluActionBlocksBundle\Event\ActionBlockExecutedEvent;
use PHPUnit\Framework\TestCase;

class ActionBlockExecutedEventTest extends TestCase
{
    public function testGetRedirect(): void
    {
        $event = new ActionBlockExecutedEvent('/test');
        self::assertEquals('/test', $event->redirect);
    }
}
