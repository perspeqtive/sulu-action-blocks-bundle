<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Execution;

use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\ConfigurationFactoryInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Event\ActionBlockExecutedEvent;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistry;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Repository\ActionBlockRepositoryInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class ActionBlockExecutor implements ActionBlockExecutorInterface
{
    public function __construct(
        private ActionBlockRepositoryInterface $actionBlockRepository,
        private ActionRegistry $actionRegistry,
        private EventDispatcherInterface $eventDispatcher,
        private ConfigurationFactoryInterface $configurationFactory
    ) {
    }

    public function execute(int $actionBlockIdentifier, array $options = []): string
    {
        $actionBlock = $this->actionBlockRepository->findById($actionBlockIdentifier);

        $action = $this->actionRegistry->getAction($actionBlock->getAction());
        if ($action instanceof ServiceActionItemInterface === false) {
            return '';
        }
        $configuration = $this->configurationFactory->create($actionBlock->getConfiguration());
        $result = $action->execute($configuration, $options);

        if (empty($result->redirect) === false) {
            $this->eventDispatcher->dispatch(
                new ActionBlockExecutedEvent($result->redirect),
            );

            return '';
        }

        return $result->html;
    }
}
