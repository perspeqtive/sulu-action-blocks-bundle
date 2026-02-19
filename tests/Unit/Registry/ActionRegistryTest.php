<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Registry;

use InvalidArgumentException;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistry;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItem;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItemForRedirect;
use PHPUnit\Framework\TestCase;
use stdClass;

use function get_class;

class ActionRegistryTest extends TestCase
{
    private ActionRegistry $registry;
    private MockServiceActionItem $action1;
    private MockServiceActionItemForRedirect $action2;

    protected function setUp(): void
    {
        $this->action1 = new MockServiceActionItem();
        $this->action2 = new MockServiceActionItemForRedirect();

        $this->registry = new ActionRegistry([$this->action1, $this->action2]);
    }

    public function testGetActions(): void
    {
        self::assertCount(2, $this->registry->getActions());
        self::assertContains($this->action1, $this->registry->getActions());
        self::assertContains($this->action2, $this->registry->getActions());
    }

    public function testGetAction(): void
    {
        self::assertSame($this->action1, $this->registry->getAction(get_class($this->action1)));
        self::assertNull($this->registry->getAction('non-existent'));
    }

    public function testValidationThrowsExceptionOnInvalidAction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Action must implement ServiceActionItemInterface');

        new ActionRegistry([new stdClass()]);
    }
}
