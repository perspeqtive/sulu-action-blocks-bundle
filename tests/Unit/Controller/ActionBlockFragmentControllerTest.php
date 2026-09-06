<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Controller;

use PERSPEQTIVE\SuluActionBlocksBundle\Controller\ActionBlockFragmentController;
use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionExecutionResult;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionBlockExecutor;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockCacheableActionItem;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function http_build_query;

final class ActionBlockFragmentControllerTest extends TestCase
{
    private const PATH = 'http://localhost/_action-block-fragment';

    private ActionBlockFragmentController $controller;
    private MockActionBlockExecutor $actionBlockExecutor;
    private UriSigner $uriSigner;

    protected function setUp(): void
    {
        $this->actionBlockExecutor = new MockActionBlockExecutor();
        $this->uriSigner = new UriSigner('test-secret');
        $this->controller = new ActionBlockFragmentController($this->actionBlockExecutor, $this->uriSigner);
    }

    public function testIndexReturnsHtmlWithSharedMaxAge(): void
    {
        $this->actionBlockExecutor->actionItemToReturn = new MockCacheableActionItem(cacheTtl: 300);
        $this->actionBlockExecutor->actionItemResult = new ActionExecutionResult('<h1>Cached</h1>');

        $response = $this->controller->index($this->buildSignedRequest(['actionItem' => 'some-block-name']));

        self::assertSame(Response::HTTP_OK, $response->getStatusCode());
        self::assertSame('<h1>Cached</h1>', $response->getContent());
        self::assertSame(300, $response->getMaxAge());
        self::assertTrue($response->headers->getCacheControlDirective('public'));
        self::assertTrue($response->headers->has(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER));
    }

    public function testIndexResolvesAndExecutesTheRequestedActionItem(): void
    {
        $actionItem = new MockCacheableActionItem();
        $this->actionBlockExecutor->actionItemToReturn = $actionItem;

        $this->controller->index($this->buildSignedRequest([
            'actionItem' => 'some-block-name',
            'options' => ['limit' => '5'],
        ]));

        self::assertSame('some-block-name', $this->actionBlockExecutor->foundActionBlockName);
        self::assertSame($actionItem, $this->actionBlockExecutor->executedActionItem);
        self::assertSame(['limit' => '5'], $this->actionBlockExecutor->executedOptions);
    }

    public function testIndexExecutesWithoutOptions(): void
    {
        $this->actionBlockExecutor->actionItemToReturn = new MockCacheableActionItem();

        $this->controller->index($this->buildSignedRequest(['actionItem' => 'some-block-name']));

        self::assertSame([], $this->actionBlockExecutor->executedOptions);
    }

    public function testIndexSendsNoStoreForNonPositiveCacheTtl(): void
    {
        $this->actionBlockExecutor->actionItemToReturn = new MockCacheableActionItem(cacheTtl: 0);

        $response = $this->controller->index($this->buildSignedRequest(['actionItem' => 'some-block-name']));

        self::assertSame('no-store, private', $response->headers->get('Cache-Control'));
        self::assertFalse($response->headers->has(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER));
    }

    public function testIndexThrowsAccessDeniedForUnsignedRequest(): void
    {
        $this->actionBlockExecutor->actionItemToReturn = new MockCacheableActionItem();

        $this->expectException(AccessDeniedHttpException::class);

        $this->controller->index(Request::create(self::PATH . '?actionItem=some-block-name'));
    }

    public function testIndexThrowsAccessDeniedForTamperedOptions(): void
    {
        $this->actionBlockExecutor->actionItemToReturn = new MockCacheableActionItem();
        $signedRequest = $this->buildSignedRequest(['actionItem' => 'some-block-name', 'options' => ['limit' => '5']]);

        $this->expectException(AccessDeniedHttpException::class);

        $this->controller->index(Request::create(
            self::PATH . '?' . str_replace('limit%5D=5', 'limit%5D=9999', (string) $signedRequest->server->get('QUERY_STRING')),
        ));
    }

    public function testIndexThrowsNotFoundWithoutActionItemParameter(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->controller->index($this->buildSignedRequest([]));
    }

    public function testIndexThrowsNotFoundForUnknownActionBlock(): void
    {
        $this->actionBlockExecutor->actionItemToReturn = null;

        $this->expectException(NotFoundHttpException::class);

        $this->controller->index($this->buildSignedRequest(['actionItem' => 'some-unknown-block']));
    }

    public function testIndexThrowsNotFoundForActionBlockThatIsNotCacheable(): void
    {
        $this->actionBlockExecutor->actionItemToReturn = new MockServiceActionItem();

        $this->expectException(NotFoundHttpException::class);

        $this->controller->index($this->buildSignedRequest(['actionItem' => 'some-block-name']));
    }

    private function buildSignedRequest(array $query): Request
    {
        $uri = self::PATH;
        if ($query !== []) {
            $uri .= '?' . http_build_query($query, '', '&');
        }

        return Request::create($this->uriSigner->sign($uri));
    }
}
