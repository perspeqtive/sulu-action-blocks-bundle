<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Execution;

use Exception;
use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Configuration;
use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\ConfigurationFactoryInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Entity\ActionBlock;
use PERSPEQTIVE\SuluActionBlocksBundle\Event\ActionBlockExecutedEvent;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistry;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Repository\ActionBlockRepositoryInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class ActionBlockExecutor implements ActionBlockExecutorInterface
{
    public function __construct(
        private ActionBlockRepositoryInterface $actionBlockRepository,
        private ActionRegistry $actionRegistry,
        private EventDispatcherInterface $eventDispatcher,
        private ConfigurationFactoryInterface $configurationFactory,
        private LoggerInterface $logger,
        private string $environment,
    ) {
    }

    /**
     * @throws Exception
     */
    public function execute(int $actionBlockIdentifier, array $options = []): string
    {
        $actionBlock = $this->getActionBlock($actionBlockIdentifier);
        if ($actionBlock === null) {
            return '';
        }

        $action = $this->getServiceActionItem($actionBlock);
        if ($action === null) {
            return '';
        }

        $configuration = $this->configurationFactory->create($actionBlock->getConfiguration());

        $result = $this->executeActionBlock($action, $configuration, $options, $actionBlock);

        $this->handleRedirect($result);

        return $result->html;
    }

    private function getActionBlock(int $actionBlockIdentifier): ?ActionBlock
    {
        $actionBlock = $this->actionBlockRepository->findById($actionBlockIdentifier);
        if ($actionBlock instanceof ActionBlock === false) {
            $this->logger->error('Action block not found: ' . $actionBlockIdentifier);
            if ($this->environment !== 'prod') {
                throw new RuntimeException('Action block not found: ' . $actionBlockIdentifier);
            }
        }

        return $actionBlock;
    }

    private function getServiceActionItem(ActionBlock $actionBlock): ?ServiceActionItemInterface
    {
        $action = $this->actionRegistry->getAction($actionBlock->getAction());
        if ($action instanceof ServiceActionItemInterface === false) {
            $this->logger->error('Action not found: ' . $actionBlock->getAction());
            if ($this->environment !== 'prod') {
                throw new RuntimeException('Action not found: ' . $actionBlock->getAction());
            }
        }

        return $action;
    }

    /**
     * @throws Exception
     */
    private function executeActionBlock(ServiceActionItemInterface $action, Configuration $configuration, array $options, ActionBlock $actionBlock): ActionExecutionResult
    {
        try {
            $result = $action->execute($configuration, $options);
        } catch (Exception $exception) {
            $this->logger->error('Action ' . $actionBlock->getAction() . ' threw unexpected exception: ' . $exception->getMessage());
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
