<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Execution;

use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Configuration;
use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\ConfigurationFactoryInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Entity\ActionBlock;
use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionBlockExecutor;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistry;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionBlockRepository;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockConfigurationFactory;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItem;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItemForRedirect;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\Symfony\MockEventDispatcher;
use PHPUnit\Framework\TestCase;

class ActionBlockExecutorTest extends TestCase
{
    private MockActionBlockRepository $repository;
    private ActionBlockExecutor $executor;
    private MockEventDispatcher $eventDispatcher;
    private ConfigurationFactoryInterface $configurationFactory;

    protected function setUp(): void
    {
        $this->repository = new MockActionBlockRepository();
        $actionRegistry = new ActionRegistry([
            new MockServiceActionItem(),
            new MockServiceActionItemForRedirect(),
        ]);
        $this->eventDispatcher = new MockEventDispatcher();
        $this->configurationFactory = new MockConfigurationFactory();
        $this->executor = new ActionBlockExecutor(
            $this->repository,
            $actionRegistry,
            $this->eventDispatcher,
            $this->configurationFactory
        );
    }

    public function testExecuteReturnsHtml(): void
    {
        $actionBlock = new ActionBlock();
        $actionBlock->setAction(MockServiceActionItem::class);
        $actionBlock->setConfiguration(['key' => 'value']);

        $this->repository->findResult = $actionBlock;

        $this->configurationFactory->configuration =
            new Configuration(
                ['key' => ['value' => 'value']]
            );
        $result = $this->executor->execute(1);

        self::assertEquals('<h1>Hello</h1>', $result);
    }

    public function testExecuteDispatchesEventOnRedirect(): void
    {
        $actionBlock = new ActionBlock();
        $actionBlock->setAction(MockServiceActionItemForRedirect::class);
        $actionBlock->setConfiguration(['redirect' => '/target-url']);

        $this->repository->findResult = $actionBlock;

        $this->configurationFactory->configuration =
            new Configuration(
                ['redirect' => ['value' => '/target-url', 'resolved' => '/target-url']]
            );

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
