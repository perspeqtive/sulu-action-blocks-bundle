<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\Sulu;

use Exception;
use Sulu\Component\Webspace\Manager\WebspaceCollection;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Sulu\Component\Webspace\Portal;
use Sulu\Component\Webspace\PortalInformation;
use Sulu\Component\Webspace\Webspace;

final class MockWebspaceManager implements WebspaceManagerInterface
{
    public ?string $urlToReturn = null;
    public ?string $receivedResourceLocator = null;
    public ?string $receivedLocale = null;

    public function findUrlByResourceLocator(
        ?string $resourceLocator,
        ?string $environment,
        string $languageCode,
        ?string $webspaceKey = null,
        ?string $domain = null,
        ?string $scheme = null,
    ): ?string {
        $this->receivedResourceLocator = $resourceLocator;
        $this->receivedLocale = $languageCode;

        return $this->urlToReturn;
    }

    public function findWebspaceByKey(?string $key): ?Webspace
    {
        return null;
    }

    public function findPortalByKey(?string $key): ?Portal
    {
        return null;
    }

    public function findPortalInformationByUrl(string $url, ?string $environment = null): ?PortalInformation
    {
        return null;
    }

    public function findPortalInformationsByUrl(string $url, ?string $environment = null): array
    {
        return [];
    }

    public function findPortalInformationsByHostIncludingSubdomains(string $host, ?string $environment = null): array
    {
        return [];
    }

    public function findPortalInformationsByWebspaceKeyAndLocale(string $webspaceKey, string $locale, ?string $environment = null): array
    {
        return [];
    }

    public function findPortalInformationsByPortalKeyAndLocale(string $portalKey, string $locale, ?string $environment = null): array
    {
        return [];
    }

    public function findUrlsByResourceLocator(string $resourceLocator, ?string $environment, string $languageCode, ?string $webspaceKey = null, ?string $domain = null, ?string $scheme = null): array
    {
        return [];
    }

    public function getPortals(): array
    {
        return [];
    }

    public function getUrls(?string $environment = null): array
    {
        return [];
    }

    public function getPortalInformations(?string $environment = null): array
    {
        return [];
    }

    public function getPortalInformationsByWebspaceKey(?string $environment, string $webspaceKey): array
    {
        return [];
    }

    public function getWebspaceCollection(): WebspaceCollection
    {
        throw new Exception('Not implemented');
    }

    public function getAllLocalesByWebspaces(): array
    {
        return [];
    }

    public function getLocalizations(): array
    {
        return [];
    }

    public function getAllLocalizations(): array
    {
        return [];
    }

    public function getAllLocales(): array
    {
        return [];
    }
}
