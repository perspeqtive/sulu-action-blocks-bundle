<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Registry;

use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionExecutionResult;

interface ServiceActionItemInterface
{
    public function getIdentifier(): string;

    public function getTitle(): string;

    public function execute(array $configuration = [], array $options = []): ActionExecutionResult;
}
