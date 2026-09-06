<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Execution;

use Exception;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionBlockExecutor;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformation;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationCollection;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionBlockFragmentRenderer;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionBlockInformationProvider;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionRegistry;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockCacheableActionItem;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItemForRedirect;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItemWithException;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\Symfony\MockEventDispatcher;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ActionBlockExecutorTest extends TestCase
{
    private ActionBlockExecutor $executorProd;
    private MockEventDispatcher $eventDispatcher;

    private TestHandler $logs;
    private MockActionBlockInformationProvider $provider;
    private MockActionRegistry $actionRegistry;
    private MockActionBlockFragmentRenderer $fragmentRenderer;
    private ActionBlockExecutor $executorDev;

    private string $existingActionInformation = 'some-block-name';

    protected function setUp(): void
    {
        $this->provider = new MockActionBlockInformationProvider();
        $this->provider->result->add(new ActionBlockInformation($this->existingActionInformation, 'Title', 'Identifier'));

        $this->actionRegistry = new MockActionRegistry();
        $this->eventDispatcher = new MockEventDispatcher();
        $this->fragmentRenderer = new MockActionBlockFragmentRenderer();

        $logger = $this->buildLogger();

        $this->executorProd = new ActionBlockExecutor(
            $this->provider,
            $this->actionRegistry,
            $this->fragmentRenderer,
            $this->eventDispatcher,
            $logger,
            'prod',
        );
        $this->executorDev = new ActionBlockExecutor(
            $this->provider,
            $this->actionRegistry,
            $this->fragmentRenderer,
            $this->eventDispatcher,
            $logger,
            'dev',
        );
    }

    public function testExecuteReturnsHtml(): void
    {
        $result = $this->executorProd->execute($this->existingActionInformation, ['key' => 'value']);

        self::assertSame('<h1>Hello</h1>', $result);
    }

    public function testExecuteDispatchesEventOnRedirect(): void
    {
        $this->actionRegistry->result = new MockServiceActionItemForRedirect();

        $options = ['redirect' => '/target-url'];

        $result = $this->executorProd->execute($this->existingActionInformation, $options);

        self::assertSame('', $result);
        self::assertSame('/target-url', $this->eventDispatcher->dispatchedEvent[0]->redirect);
    }

    public function testExecuteReturnsEmptyStringIfActionNotFound(): void
    {
        $this->actionRegistry->result = null;

        $result = $this->executorProd->execute($this->existingActionInformation);

        self::assertSame('', $result);
    }

    public function testExecuteThrowsExceptionIfActionInformationNotFoundInDev(): void
    {
        $this->provider->result = new ActionBlockInformationCollection();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Action block not found: some-unknown-action-block');

        $this->executorDev->execute('some-unknown-action-block');
    }

    public function testExecuteReturnsEmptyStringIfActionInformationNotFoundInProd(): void
    {
        $this->provider->result = new ActionBlockInformationCollection();

        $result = $this->executorProd->execute('some-unknown-action-block');

        self::assertSame('', $result);
    }

    public function testExecuteThrowsExceptionIfActionNotFoundInDev(): void
    {
        $this->actionRegistry->result = null;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Action not found: Identifier');

        $this->executorDev->execute($this->existingActionInformation);
    }

    public function testExecuteThrowsExceptionIfActionThrowsExceptionInDev(): void
    {
        $this->actionRegistry->result = new MockServiceActionItemWithException();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Action Exception');

        $this->executorDev->execute($this->existingActionInformation);
    }

    public function testExecuteReturnsEmptyHtmlIfActionThrowsExceptionInProd(): void
    {
        $this->actionRegistry->result = new MockServiceActionItemWithException();

        $result = $this->executorProd->execute($this->existingActionInformation);

        self::assertSame('', $result);
        self::assertTrue($this->logs->hasErrorRecords());
    }

    public function testExecuteRendersCacheableActionBlockAsFragment(): void
    {
        $this->actionRegistry->result = new MockCacheableActionItem();

        $result = $this->executorProd->execute($this->existingActionInformation, ['limit' => 5]);

        self::assertSame('<esi:include src="/fragment" />', $result);
        self::assertSame($this->existingActionInformation, $this->fragmentRenderer->receivedActionBlockName);
        self::assertSame(['limit' => 5], $this->fragmentRenderer->receivedOptions);
    }

    public function testExecuteDoesNotExecuteCacheableActionBlockWhileRenderingTheFragment(): void
    {
        $actionItem = new MockCacheableActionItem();
        $this->actionRegistry->result = $actionItem;

        $this->executorProd->execute($this->existingActionInformation);

        self::assertNull($actionItem->receivedOptions);
    }

    public function testExecuteRendersCacheableActionBlockInlineWhenFragmentRenderingIsUnavailable(): void
    {
        $this->actionRegistry->result = new MockCacheableActionItem();
        $this->fragmentRenderer->fragmentToReturn = null;

        $result = $this->executorProd->execute($this->existingActionInformation);

        self::assertSame('<h1>Cached</h1>', $result);
    }

    public function testExecuteDoesNotRenderFragmentForActionBlockThatIsNotCacheable(): void
    {
        $result = $this->executorProd->execute($this->existingActionInformation);

        self::assertSame('<h1>Hello</h1>', $result);
        self::assertNull($this->fragmentRenderer->receivedActionBlockName);
    }

    public function testFindActionItemReturnsNullForUnknownActionBlockInProd(): void
    {
        $this->provider->result = new ActionBlockInformationCollection();

        self::assertNull($this->executorProd->findActionItem('some-unknown-action-block'));
    }

    public function testFindActionItemReturnsTheRegisteredActionItem(): void
    {
        $actionItem = new MockCacheableActionItem();
        $this->actionRegistry->result = $actionItem;

        self::assertSame($actionItem, $this->executorProd->findActionItem($this->existingActionInformation));
    }

    public function testExecuteActionItemPassesOptionsToTheActionItem(): void
    {
        $actionItem = new MockCacheableActionItem();

        $result = $this->executorProd->executeActionItem($actionItem, ['limit' => 5]);

        self::assertSame('<h1>Cached</h1>', $result->html);
        self::assertSame(['limit' => 5], $actionItem->receivedOptions);
    }

    public function testExecuteActionItemReturnsEmptyResultOnExceptionInProd(): void
    {
        $result = $this->executorProd->executeActionItem(new MockServiceActionItemWithException());

        self::assertSame('', $result->html);
        self::assertTrue($this->logs->hasErrorRecords());
    }

    public function testExecuteActionItemThrowsOnExceptionInDev(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Action Exception');

        $this->executorDev->executeActionItem(new MockServiceActionItemWithException());
    }

    private function buildLogger(): Logger
    {
        $this->logs = new TestHandler();

        return new Logger('tests', [$this->logs]);
    }
}
