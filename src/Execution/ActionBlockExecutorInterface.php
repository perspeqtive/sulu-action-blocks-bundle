<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Execution;

interface ActionBlockExecutorInterface
{
    public function execute(string $actionBlockName, array $options = []): string;
}
