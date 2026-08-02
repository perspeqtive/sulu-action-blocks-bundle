<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Execution;

use Exception;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionBlockExecutor;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformation;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationCollection;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionBlockInformationProvider;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionRegistry;
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
    private ActionBlockExecutor $executorDev;

    protected function setUp(): void
    {
        $this->provider = new MockActionBlockInformationProvider();
        $this->provider->result->add(new ActionBlockInformation('some-block-name', 'Title', 'Identifier'));

        $this->actionRegistry = new MockActionRegistry();
        $this->eventDispatcher = new MockEventDispatcher();

        $logger = $this->buildLogger();

        $this->executorProd = new ActionBlockExecutor(
            $this->provider,
            $this->actionRegistry,
            $this->eventDispatcher,
            $logger,
            'prod',
        );
        $this->executorDev = new ActionBlockExecutor(
            $this->provider,
            $this->actionRegistry,
            $this->eventDispatcher,
            $logger,
            'dev',
        );
    }

    public function testExecuteReturnsHtml(): void
    {
        $result = $this->executorProd->execute('some-block-name', ['key' => 'value']);

        self::assertSame('<h1>Hello</h1>', $result);
    }

    public function testExecuteDispatchesEventOnRedirect(): void
    {
        $this->actionRegistry->result = new MockServiceActionItemForRedirect();

        $options = ['redirect' => '/target-url'];

        $result = $this->executorProd->execute('some-block-name', $options);

        self::assertSame('', $result);
        self::assertSame('/target-url', $this->eventDispatcher->dispatchedEvent[0]->redirect);
    }

    public function testExecuteReturnsEmptyStringIfActionNotFound(): void
    {
        $result = $this->executorProd->execute('some-unknown-action-block');

        self::assertSame('', $result);
    }

    public function testExecuteThrowsExceptionIfActionInformationNotFoundInDev(): void
    {
        $this->provider->result = new ActionBlockInformationCollection();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Action block not found: some-unknown-action-block');

        $this->executorDev->execute('some-unknown-action-block');
    }

    public function testExecuteThrowsExceptionIfActionNotFoundInDev(): void
    {
        $this->actionRegistry->result = null;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Action not found: Identifier');

        $this->executorDev->execute('some-block-name');
    }

    public function testExecuteThrowsExceptionIfActionThrowsExceptionInDev(): void
    {
        $this->actionRegistry->result = new MockServiceActionItemWithException();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Action Exception');

        $this->executorDev->execute('some-block-name');
    }

    public function testExecuteReturnsEmptyHtmlIfActionThrowsExceptionInProd(): void
    {
        $this->actionRegistry->result = new MockServiceActionItemWithException();

        $result = $this->executorProd->execute('some-block-name');

        self::assertSame('', $result);
        self::assertTrue($this->logs->hasErrorRecords());
    }

    private function buildLogger(): Logger
    {
        $this->logs = new TestHandler();

        return new Logger('tests', [$this->logs]);
    }
}
