<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\Symfony;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

final class MockUrlGenerator implements UrlGeneratorInterface
{
    public ?string $receivedRoute = null;
    public ?array $receivedParameters = null;
    public ?int $receivedReferenceType = null;

    public function __construct(
        public string $generatedUrl = 'http://localhost/_action-block-fragment?actionItem=some-block-name',
        private RequestContext $context = new RequestContext(),
    ) {
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        $this->receivedRoute = $name;
        $this->receivedParameters = $parameters;
        $this->receivedReferenceType = $referenceType;

        return $this->generatedUrl;
    }

    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
    }

    public function getContext(): RequestContext
    {
        return $this->context;
    }
}
