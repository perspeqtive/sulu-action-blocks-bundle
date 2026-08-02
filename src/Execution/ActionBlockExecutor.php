<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Execution;

use Exception;
use PERSPEQTIVE\SuluActionBlocksBundle\Event\ActionBlockExecutedEvent;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformation;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationProviderInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistry;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistryInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class ActionBlockExecutor implements ActionBlockExecutorInterface
{
    public function __construct(
        private ActionBlockInformationProviderInterface $actionBlockInformationProvider,
        private ActionRegistryInterface                 $actionRegistry,
        private EventDispatcherInterface                $eventDispatcher,
        private LoggerInterface                         $logger,
        private string                                  $environment,
    ) {
    }

    /**
     * @throws Exception
     */
    public function execute(string $actionBlockName, array $options = []): string
    {
        $actionBlockInformation = $this->getActionBlock($actionBlockName);
        if ($actionBlockInformation === null) {
            return '';
        }

        $action = $this->getServiceActionItem($actionBlockInformation);
        if ($action === null) {
            return '';
        }

        $result = $this->executeActionBlock($action, $options);

        $this->handleRedirect($result);

        return $result->html;
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

    /**
     * @throws Exception
     */
    private function executeActionBlock(ServiceActionItemInterface $action, array $options): ActionExecutionResult
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
