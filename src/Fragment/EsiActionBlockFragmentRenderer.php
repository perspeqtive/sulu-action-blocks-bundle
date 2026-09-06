<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Fragment;

use LogicException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function str_starts_with;
use function strlen;
use function substr;

readonly class EsiActionBlockFragmentRenderer implements ActionBlockFragmentRendererInterface
{
    public const ROUTE = 'perspeqtive_sulu_action_blocks_fragment';

    private const RENDERER = 'esi';

    public function __construct(
        private FragmentHandler $fragmentHandler,
        private UrlGeneratorInterface $urlGenerator,
        private UriSigner $uriSigner,
        private RequestStack $requestStack,
        private LoggerInterface $logger,
    ) {
    }

    public function render(string $actionBlockName, array $options = []): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            $this->logger->error(
                'Action block "' . $actionBlockName . '" cannot be rendered as a fragment outside of a request.',
            );

            return null;
        }

        try {
            return $this->fragmentHandler->render(
                $this->stripHost($this->buildSignedUri($actionBlockName, $options), $request->getSchemeAndHttpHost()),
                self::RENDERER,
            );
        } catch (LogicException $exception) {
            $this->logger->error(
                'Action block "' . $actionBlockName . '" cannot be rendered as an ESI fragment, '
                . 'falling back to inline rendering. Enable "framework.esi" to cache it separately: '
                . $exception->getMessage(),
            );

            return null;
        }
    }

    private function buildSignedUri(string $actionBlockName, array $options): string
    {
        return $this->uriSigner->sign(
            $this->urlGenerator->generate(
                self::ROUTE,
                ['actionItem' => $actionBlockName, 'options' => $options],
                UrlGeneratorInterface::ABSOLUTE_URL,
            ),
        );
    }

    private function stripHost(string $signedUri, string $schemeAndHttpHost): string
    {
        if (str_starts_with($signedUri, $schemeAndHttpHost) === false) {
            return $signedUri;
        }

        return substr($signedUri, strlen($schemeAndHttpHost));
    }
}
