<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Execution;

use Exception;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Configuration;
use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\ConfigurationFactoryInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Entity\ActionBlock;
use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionBlockExecutor;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistry;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionBlockRepository;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockConfigurationFactory;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItem;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItemForRedirect;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItemWithException;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\Symfony\MockEventDispatcher;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ActionBlockExecutorTest extends TestCase
{
    private MockActionBlockRepository $repository;
    private ActionBlockExecutor $executor;
    private MockEventDispatcher $eventDispatcher;
    private ConfigurationFactoryInterface $configurationFactory;

    private TestHandler $logs;

    protected function setUp(): void
    {
        $this->repository = new MockActionBlockRepository();
        $actionRegistry = new ActionRegistry([
            new MockServiceActionItem(),
            new MockServiceActionItemForRedirect(),
            new MockServiceActionItemWithException(),
        ]);
        $this->eventDispatcher = new MockEventDispatcher();
        $this->configurationFactory = new MockConfigurationFactory();
        $this->logs = new TestHandler();
        $logger = new Logger('tests', [$this->logs]);
        $this->executor = new ActionBlockExecutor(
            $this->repository,
            $actionRegistry,
            $this->eventDispatcher,
            $this->configurationFactory,
            $logger,
            'prod',
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
                ['key' => ['value' => 'value']],
            );
        $result = $this->executor->execute(1);

        self::assertSame('<h1>Hello</h1>', $result);
    }

    public function testExecuteDispatchesEventOnRedirect(): void
    {
        $actionBlock = new ActionBlock();
        $actionBlock->setAction(MockServiceActionItemForRedirect::class);
        $actionBlock->setConfiguration(['redirect' => '/target-url']);

        $this->repository->findResult = $actionBlock;

        $this->configurationFactory->configuration =
            new Configuration(
                ['redirect' => ['value' => '/target-url', 'resolved' => '/target-url']],
            );

        $result = $this->executor->execute(1);

        self::assertSame('', $result);
        self::assertSame('/target-url', $this->eventDispatcher->dispatchedEvent[0]->redirect);
    }

    public function testExecuteReturnsEmptyStringIfActionNotFound(): void
    {
        $actionBlock = new ActionBlock();
        $actionBlock->setAction('non_existent');

        $this->repository->findResult = $actionBlock;

        $result = $this->executor->execute(1);

        self::assertSame('', $result);
    }

    public function testExecuteThrowsExceptionIfActionBlockNotFoundInDev(): void
    {
        $this->executor = new ActionBlockExecutor(
            $this->repository,
            new ActionRegistry([]),
            $this->eventDispatcher,
            $this->configurationFactory,
            new Logger('tests', [$this->logs]),
            'dev',
        );

        $this->repository->findResult = null;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Action block not found: 1');

        $this->executor->execute(1);
    }

    public function testExecuteThrowsExceptionIfActionNotFoundInDev(): void
    {
        $this->executor = new ActionBlockExecutor(
            $this->repository,
            new ActionRegistry([]),
            $this->eventDispatcher,
            $this->configurationFactory,
            new Logger('tests', [$this->logs]),
            'dev',
        );

        $actionBlock = new ActionBlock();
        $actionBlock->setAction('non_existent');
        $this->repository->findResult = $actionBlock;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Action not found: non_existent');

        $this->executor->execute(1);
    }

    public function testExecuteThrowsExceptionIfActionThrowsExceptionInDev(): void
    {
        $this->executor = new ActionBlockExecutor(
            $this->repository,
            new ActionRegistry([new MockServiceActionItemWithException()]),
            $this->eventDispatcher,
            $this->configurationFactory,
            new Logger('tests', [$this->logs]),
            'dev',
        );

        $actionBlock = new ActionBlock();
        $actionBlock->setAction(MockServiceActionItemWithException::class);
        $this->repository->findResult = $actionBlock;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Action Exception');

        $this->executor->execute(1);
    }

    public function testExecuteReturnsEmptyHtmlIfActionThrowsExceptionInProd(): void
    {
        $actionBlock = new ActionBlock();
        $actionBlock->setAction(MockServiceActionItemWithException::class);
        $this->repository->findResult = $actionBlock;

        $result = $this->executor->execute(1);

        self::assertSame('', $result);
        self::assertTrue($this->logs->hasErrorRecords());
    }
}
