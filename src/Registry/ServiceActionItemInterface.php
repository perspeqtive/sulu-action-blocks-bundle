<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Registry;

use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Configuration;
use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionExecutionResult;

interface ServiceActionItemInterface
{
    public function getIdentifier(): string;

    public function getTitle(): string;

    public function getConfigurationBlock(): string;

    public function execute(Configuration $configuration, array $options = []): ActionExecutionResult;
}
