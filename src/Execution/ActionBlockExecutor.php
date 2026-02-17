<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Execution;

use PERSPEQTIVE\SuluActionBlockBundle\Event\ActionBlockExecutedEvent;
use PERSPEQTIVE\SuluActionBlockBundle\Registry\ActionRegistry;
use PERSPEQTIVE\SuluActionBlockBundle\Registry\ServiceActionItemInterface;
use PERSPEQTIVE\SuluActionBlockBundle\Repository\ActionBlockRepository;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class ActionBlockExecutor
{
    public function __construct(
        private ActionBlockRepository $actionBlockRepository,
        private ActionRegistry $actionRegistry,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function execute(int $actionBlockIdentifier, array $options = []): string
    {
        $actionBlock = $this->actionBlockRepository->findById($actionBlockIdentifier);

        $action = $this->actionRegistry->getAction($actionBlock->getAction());
        if ($action instanceof ServiceActionItemInterface === false) {
            return '';
        }

        $result = $action->execute($actionBlock->getConfiguration(), $options);

        if (empty($result->redirect) === false) {
            $this->eventDispatcher->dispatch(
                new ActionBlockExecutedEvent($result->redirect),
            );

            return '';
        }

        return $result->html;
    }
}
