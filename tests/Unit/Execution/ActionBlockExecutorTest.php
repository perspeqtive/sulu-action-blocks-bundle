<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Tests\Unit\Execution;

use PERSPEQTIVE\SuluActionBlockBundle\Entity\ActionBlock;
use PERSPEQTIVE\SuluActionBlockBundle\Execution\ActionBlockExecutor;
use PERSPEQTIVE\SuluActionBlockBundle\Registry\ActionRegistry;
use PERSPEQTIVE\SuluActionBlockBundle\Tests\Unit\Mocks\MockActionBlockRepository;
use PERSPEQTIVE\SuluActionBlockBundle\Tests\Unit\Mocks\MockServiceActionItem;
use PERSPEQTIVE\SuluActionBlockBundle\Tests\Unit\Mocks\MockServiceActionItemForRedirect;
use PERSPEQTIVE\SuluActionBlockBundle\Tests\Unit\Mocks\Symfony\MockEventDispatcher;
use PHPUnit\Framework\TestCase;

class ActionBlockExecutorTest extends TestCase
{
    private MockActionBlockRepository $repository;
    private ActionBlockExecutor $executor;
    private ActionRegistry $actionRegistry;
    private MockEventDispatcher $eventDispatcher;

    protected function setUp(): void
    {
        $this->repository = new MockActionBlockRepository();
        $this->actionRegistry = new ActionRegistry([
            new MockServiceActionItem(),
            new MockServiceActionItemForRedirect(),
        ]);
        $this->eventDispatcher = new MockEventDispatcher();
        $this->executor = new ActionBlockExecutor(
            $this->repository,
            $this->actionRegistry,
            $this->eventDispatcher,
        );
    }

    public function testExecuteReturnsHtml(): void
    {
        $actionBlock = new ActionBlock();
        $actionBlock->setAction(MockServiceActionItem::class);
        $actionBlock->setConfiguration(['key' => 'value']);

        $this->repository->findResult = $actionBlock;

        $result = $this->executor->execute(1);

        self::assertEquals('<h1>Hello</h1>', $result);
    }

    public function testExecuteDispatchesEventOnRedirect(): void
    {
        $actionBlock = new ActionBlock();
        $actionBlock->setAction(MockServiceActionItemForRedirect::class);
        $actionBlock->setConfiguration(['redirect' => '/target-url']);

        $this->repository->findResult = $actionBlock;

        $result = $this->executor->execute(1);

        self::assertEquals('', $result);
        self::assertEquals('/target-url', $this->eventDispatcher->dispatchedEvent[0]->redirect);
    }

    public function testExecuteReturnsEmptyStringIfActionNotFound(): void
    {
        $actionBlock = new ActionBlock();
        $actionBlock->setAction('non_existent');

        $this->repository->findResult = $actionBlock;

        $result = $this->executor->execute(1);

        self::assertEquals('', $result);
    }
}
