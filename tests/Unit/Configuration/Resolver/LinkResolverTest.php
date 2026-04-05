<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Configuration\Resolver;

use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Resolver\LinkResolver;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\Sulu\MockDocumentManager;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\Sulu\MockWebspaceManager;
use PHPUnit\Framework\TestCase;
use Sulu\Component\Webspace\Analyzer\Attributes\RequestAttributes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class LinkResolverTest extends TestCase
{
    private MockDocumentManager $documentManager;
    private MockWebspaceManager $webspaceManager;
    private RequestStack $requestStack;
    private LinkResolver $linkResolver;

    protected function setUp(): void
    {
        $this->documentManager = new MockDocumentManager();
        $this->webspaceManager = new MockWebspaceManager();
        $this->requestStack = new RequestStack();
        $this->linkResolver = new LinkResolver(
            $this->documentManager,
            $this->webspaceManager,
            $this->requestStack,
        );
    }

    public function testSupportsReturnsTrueForActionLink(): void
    {
        self::assertTrue($this->linkResolver->supports('action-link'));
    }

    public function testSupportsReturnsFalseForOtherType(): void
    {
        self::assertFalse($this->linkResolver->supports('other'));
    }

    public function testResolveResolvesLinkSuccessfully(): void
    {
        $data = ['value' => 'some-uuid'];
        $locale = 'en';

        $request = new Request();
        $request->attributes->set('_sulu', new RequestAttributes(['locale' => $locale]));
        $this->requestStack->push($request);

        $mockPage = new class {
            public function getResourceSegment(): string
            {
                return '/some-path';
            }
        };
        $this->documentManager->documentToReturn = $mockPage;
        $this->webspaceManager->urlToReturn = 'https://example.com/some-path';

        $result = $this->linkResolver->resolve($data);

        self::assertSame('some-uuid', $this->documentManager->receivedUuid);
        self::assertSame($locale, $this->documentManager->receivedLocale);
        self::assertSame('/some-path', $this->webspaceManager->receivedResourceLocator);
        self::assertSame($locale, $this->webspaceManager->receivedLocale);
        self::assertSame('https://example.com/some-path', $result['resolved']);
    }

    public function testResolveReturnsDataUnchangedOnException(): void
    {
        $data = ['value' => 'some-uuid'];
        $this->documentManager->documentToReturn = null;

        $result = $this->linkResolver->resolve($data);

        self::assertSame($data, $result);
        self::assertArrayNotHasKey('resolved', $result);
    }

    public function testResolveUsesDefaultLocaleWhenNoRequest(): void
    {
        $data = ['value' => 'some-uuid'];

        $mockPage = new class {
            public function getResourceSegment(): string
            {
                return '/some-path';
            }
        };
        $this->documentManager->documentToReturn = $mockPage;
        $this->webspaceManager->urlToReturn = '/de/some-path';

        $result = $this->linkResolver->resolve($data);

        self::assertSame('de', $this->documentManager->receivedLocale);
        self::assertSame('de', $this->webspaceManager->receivedLocale);
        self::assertSame('/de/some-path', $result['resolved']);
    }

    public function testResolveUsesDefaultLocaleWhenSuluAttributesPresentButNoLocale(): void
    {
        $data = ['value' => 'some-uuid'];

        $request = new Request();
        $request->attributes->set('_sulu', new RequestAttributes([]));
        $this->requestStack->push($request);

        $mockPage = new class {
            public function getResourceSegment(): string
            {
                return '/some-path';
            }
        };
        $this->documentManager->documentToReturn = $mockPage;
        $this->webspaceManager->urlToReturn = '/de/some-path';

        $result = $this->linkResolver->resolve($data);

        self::assertSame('de', $this->documentManager->receivedLocale);
        self::assertSame('de', $this->webspaceManager->receivedLocale);
        self::assertSame('/de/some-path', $result['resolved']);
    }

    public function testResolveUsesDefaultLocaleWhenRequestExistsButNoSuluAttributes(): void
    {
        $data = ['value' => 'some-uuid'];

        $request = new Request();
        $this->requestStack->push($request);

        $mockPage = new class {
            public function getResourceSegment(): string
            {
                return '/some-path';
            }
        };
        $this->documentManager->documentToReturn = $mockPage;
        $this->webspaceManager->urlToReturn = '/de/some-path';

        $result = $this->linkResolver->resolve($data);

        self::assertSame('de', $this->documentManager->receivedLocale);
        self::assertSame('de', $this->webspaceManager->receivedLocale);
        self::assertSame('/de/some-path', $result['resolved']);
    }

    public function testResolveUsesDefaultLocaleWhenSuluAttributesAreNotRequestAttributes(): void
    {
        $data = ['value' => 'some-uuid'];

        $request = new Request();
        $request->attributes->set('_sulu', 'not-an-instance-of-request-attributes');
        $this->requestStack->push($request);

        $mockPage = new class {
            public function getResourceSegment(): string
            {
                return '/some-path';
            }
        };
        $this->documentManager->documentToReturn = $mockPage;
        $this->webspaceManager->urlToReturn = '/de/some-path';

        $result = $this->linkResolver->resolve($data);

        self::assertSame('de', $this->documentManager->receivedLocale);
        self::assertSame('de', $this->webspaceManager->receivedLocale);
        self::assertSame('/de/some-path', $result['resolved']);
    }
}
