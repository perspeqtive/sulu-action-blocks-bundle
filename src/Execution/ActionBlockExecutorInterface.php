<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Execution;

interface ActionBlockExecutorInterface
{
    public function execute(int $actionBlockIdentifier, array $options = []): string;
}
