<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Controller;

use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionBlockExecutorInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\CacheableActionItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UriSigner;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class ActionBlockFragmentController
{
    public function __construct(
        private ActionBlockExecutorInterface $actionBlockExecutor,
        private UriSigner $uriSigner,
    ) {
    }

    public function index(Request $request): Response
    {
        if ($this->uriSigner->checkRequest($request) === false) {
            throw new AccessDeniedHttpException('The action block fragment URI is not signed correctly.');
        }

        $actionBlockName = $request->query->getString('actionItem');
        if ($actionBlockName === '') {
            throw new NotFoundHttpException('No action block was requested.');
        }

        $action = $this->actionBlockExecutor->findActionItem($actionBlockName);
        if ($action instanceof CacheableActionItemInterface === false) {
            throw new NotFoundHttpException('Action block "' . $actionBlockName . '" is not cacheable.');
        }

        $result = $this->actionBlockExecutor->executeActionItem($action, $request->query->all('options'));

        return $this->buildResponse($result->html, $action->getCacheTtl());
    }

    private function buildResponse(string $html, int $cacheTtl): Response
    {
        $response = new Response($html);
        if ($cacheTtl < 1) {
            $response->headers->set('Cache-Control', 'no-store, private');

            return $response;
        }

        $response->setPublic();
        $response->setSharedMaxAge($cacheTtl);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, '');

        return $response;
    }
}
