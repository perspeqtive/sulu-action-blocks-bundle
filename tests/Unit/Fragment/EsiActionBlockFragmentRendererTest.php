<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Fragment;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PERSPEQTIVE\SuluActionBlocksBundle\Fragment\EsiActionBlockFragmentRenderer;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\Symfony\MockFragmentRenderer;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\Symfony\MockUrlGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class EsiActionBlockFragmentRendererTest extends TestCase
{
    private MockUrlGenerator $urlGenerator;
    private MockFragmentRenderer $fragmentRenderer;
    private RequestStack $requestStack;
    private UriSigner $uriSigner;
    private TestHandler $logs;

    protected function setUp(): void
    {
        $this->urlGenerator = new MockUrlGenerator();
        $this->fragmentRenderer = new MockFragmentRenderer();
        $this->requestStack = new RequestStack();
        $this->requestStack->push(Request::create('http://localhost/some-page'));
        $this->uriSigner = new UriSigner('test-secret');
        $this->logs = new TestHandler();
    }

    public function testRenderReturnsFragmentContent(): void
    {
        $result = $this->buildRenderer()->render('some-block-name', ['limit' => 5]);

        self::assertSame('<esi:include src="/fragment" />', $result);
    }

    public function testRenderGeneratesAbsoluteUrlForTheFragmentRoute(): void
    {
        $this->buildRenderer()->render('some-block-name', ['limit' => 5]);

        self::assertSame(EsiActionBlockFragmentRenderer::ROUTE, $this->urlGenerator->receivedRoute);
        self::assertSame(UrlGeneratorInterface::ABSOLUTE_URL, $this->urlGenerator->receivedReferenceType);
        self::assertSame(
            ['actionItem' => 'some-block-name', 'options' => ['limit' => 5]],
            $this->urlGenerator->receivedParameters,
        );
    }

    public function testRenderPassesSignedHostRelativeUriToTheFragmentHandler(): void
    {
        $this->buildRenderer()->render('some-block-name');

        $uri = (string) $this->fragmentRenderer->receivedUri;

        self::assertStringStartsWith('/_action-block-fragment?', $uri);
        self::assertTrue($this->uriSigner->check('http://localhost' . $uri));
    }

    public function testRenderKeepsAbsoluteUriWhenHostDoesNotMatchTheCurrentRequest(): void
    {
        $this->urlGenerator->generatedUrl = 'https://other-host.example/_action-block-fragment?actionItem=a';

        $this->buildRenderer()->render('some-block-name');

        self::assertStringStartsWith('https://other-host.example/', (string) $this->fragmentRenderer->receivedUri);
    }

    public function testRenderReturnsNullAndLogsWhenEsiIsNotEnabled(): void
    {
        $renderer = new EsiActionBlockFragmentRenderer(
            new FragmentHandler($this->requestStack, [], false),
            $this->urlGenerator,
            $this->uriSigner,
            $this->requestStack,
            new Logger('tests', [$this->logs]),
        );

        self::assertNull($renderer->render('some-block-name'));
        self::assertTrue($this->logs->hasErrorRecords());
    }

    public function testRenderReturnsNullAndLogsWithoutCurrentRequest(): void
    {
        $renderer = new EsiActionBlockFragmentRenderer(
            new FragmentHandler(new RequestStack(), [$this->fragmentRenderer], false),
            $this->urlGenerator,
            $this->uriSigner,
            new RequestStack(),
            new Logger('tests', [$this->logs]),
        );

        self::assertNull($renderer->render('some-block-name'));
        self::assertTrue($this->logs->hasErrorRecords());
    }

    private function buildRenderer(): EsiActionBlockFragmentRenderer
    {
        return new EsiActionBlockFragmentRenderer(
            new FragmentHandler($this->requestStack, [$this->fragmentRenderer], false),
            $this->urlGenerator,
            $this->uriSigner,
            $this->requestStack,
            new Logger('tests', [$this->logs]),
        );
    }
}
