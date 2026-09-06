<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Execution;

use Exception;
use PERSPEQTIVE\SuluActionBlocksBundle\Event\ActionBlockExecutedEvent;
use PERSPEQTIVE\SuluActionBlocksBundle\Fragment\ActionBlockFragmentRendererInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformation;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationProviderInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistryInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\CacheableActionItemInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class ActionBlockExecutor implements ActionBlockExecutorInterface
{
    public function __construct(
        private ActionBlockInformationProviderInterface $actionBlockInformationProvider,
        private ActionRegistryInterface $actionRegistry,
        private ActionBlockFragmentRendererInterface $fragmentRenderer,
        private EventDispatcherInterface $eventDispatcher,
        private LoggerInterface $logger,
        private string $environment,
    ) {
    }

    /**
     * @throws Exception
     */
    public function execute(string $actionBlockName, array $options = []): string
    {
        $action = $this->findActionItem($actionBlockName);
        if ($action === null) {
            return '';
        }

        $fragment = $this->renderAsFragment($action, $actionBlockName, $options);
        if ($fragment !== null) {
            return $fragment;
        }

        $result = $this->executeActionItem($action, $options);

        $this->handleRedirect($result);

        return $result->html;
    }

    public function findActionItem(string $actionBlockName): ?ServiceActionItemInterface
    {
        $actionBlockInformation = $this->getActionBlock($actionBlockName);
        if ($actionBlockInformation === null) {
            return null;
        }

        return $this->getServiceActionItem($actionBlockInformation);
    }

    /**
     * @throws Exception
     */
    public function executeActionItem(ServiceActionItemInterface $action, array $options = []): ActionExecutionResult
    {
        try {
            $result = $action->execute($options);
        } catch (Exception $exception) {
            $this->logger->error('Action ' . $action->getTitle() . ' threw unexpected exception: ' . $exception->getMessage());
            $this->logger->error($exception->getTraceAsString());
            if ($this->environment !== 'prod') {
                throw $exception;
            }

            return new ActionExecutionResult();
        }

        return $result;
    }

    private function renderAsFragment(ServiceActionItemInterface $action, string $actionBlockName, array $options): ?string {
        if ($action instanceof CacheableActionItemInterface === false) {
            return null;
        }

        return $this->fragmentRenderer->render($actionBlockName, $options);
    }

    private function getActionBlock(string $actionBlockName): ?ActionBlockInformation
    {
        $actionBlockInformation = $this->actionBlockInformationProvider->provide()->findByBlockName($actionBlockName);
        if ($actionBlockInformation instanceof ActionBlockInformation === false) {
            $this->logger->error('Action block not found: ' . $actionBlockName);
            if ($this->environment !== 'prod') {
                throw new RuntimeException('Action block not found: ' . $actionBlockName);
            }
        }

        return $actionBlockInformation;
    }

    private function getServiceActionItem(ActionBlockInformation $actionBlockInformation): ?ServiceActionItemInterface
    {
        $action = $this->actionRegistry->getAction($actionBlockInformation->identifier);
        if ($action instanceof ServiceActionItemInterface === false) {
            $this->logger->error('Action not found: ' . $actionBlockInformation->identifier);
            if ($this->environment !== 'prod') {
                throw new RuntimeException('Action not found: ' . $actionBlockInformation->identifier);
            }
        }

        return $action;
    }

    private function handleRedirect(ActionExecutionResult $result): void
    {
        if (empty($result->redirect) === true) {
            return;
        }

        $this->eventDispatcher->dispatch(
            new ActionBlockExecutedEvent($result->redirect),
        );
    }
}
