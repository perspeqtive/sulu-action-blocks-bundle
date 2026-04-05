<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Resolver;

use Exception;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Sulu\Component\Webspace\Analyzer\Attributes\RequestAttributes;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class LinkResolver implements TypeResolverInterface
{
    public function __construct(
        private DocumentManagerInterface $documentManager,
        private WebspaceManagerInterface $webspaceManager,
        private RequestStack $requestStack,
    ) {
    }

    public function resolve(array $data): array
    {
        $locale = $this->getLocale();
        try {
            $page = $this->documentManager->find($data['value'], $locale);
            $data['resolved'] = $this->webspaceManager->findUrlByResourceLocator(
                $page->getResourceSegment(),
                null,
                $locale,
            );
        } catch (Exception) {
        }

        return $data;
    }

    private function getLocale(): string
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest) {
            return 'de';
        }

        $suluAttributes = $currentRequest->attributes->get('_sulu');
        if (!$suluAttributes instanceof RequestAttributes) {
            return 'de';
        }

        return $suluAttributes->getAttribute('locale', 'de');
    }

    public function supports(string $type): bool
    {
        return $type === 'action-link';
    }
}
