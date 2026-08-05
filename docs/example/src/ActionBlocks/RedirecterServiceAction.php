<?php

declare(strict_types=1);

namespace App\ActionBlocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionExecutionResult;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;

final class RedirecterServiceAction implements ServiceActionItemInterface
{
    public function __construct(
        private readonly FormHandlerServiceInterface $formHandlerService,
    ) {
    }

    public function getIdentifier(): string
    {
        return self::class;
    }

    public function getTitle(): string
    {
        return 'Custom form handling with redirect';
    }

    public function getConfigurationBlock(): ?string
    {
        return null;
    }

    public function execute(array $options = []): ActionExecutionResult
    {
        return new ActionExecutionResult(
            redirect: $this->formHandlerService->handleForm($options),
        );
    }

}
