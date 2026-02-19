<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Registry;

use InvalidArgumentException;

readonly class ActionRegistry
{
    /**
     * @param iterable<ServiceActionItemInterface> $actions
     */
    public function __construct(private iterable $actions)
    {
        $this->validateActions();
    }

    /**
     * @return iterable<ServiceActionItemInterface>
     */
    public function getActions(): iterable
    {
        return $this->actions;
    }

    private function validateActions(): void
    {
        foreach ($this->actions as $action) {
            if ($action instanceof ServiceActionItemInterface === true) {
                continue;
            }
            throw new InvalidArgumentException('Action must implement ServiceActionItemInterface: ' . $action::class);
        }
    }

    public function getAction(string $actionBlockIdentifier): ?ServiceActionItemInterface
    {
        foreach ($this->actions as $action) {
            if ($action->getIdentifier() === $actionBlockIdentifier) {
                return $action;
            }
        }

        return null;
    }
}
