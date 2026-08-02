<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Registry;

interface ActionRegistryInterface
{
    /**
     * @return iterable<ServiceActionItemInterface>
     */
    public function getActions(): iterable;

    public function getAction(string $actionBlockIdentifier): ?ServiceActionItemInterface;
}
