<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Registry;

use PERSPEQTIVE\SuluActionBlockBundle\Execution\ActionExecutionResult;

interface ServiceActionItemInterface
{
    public function getIdentifier(): string;

    public function getTitle(): string;

    public function execute(array $configuration = [], array $options = []): ActionExecutionResult;
}
