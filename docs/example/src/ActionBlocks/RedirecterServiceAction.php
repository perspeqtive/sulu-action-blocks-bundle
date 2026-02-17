<?php

declare(strict_types=1);

namespace App\ActionBlocks;

use PERSPEQTIVE\SuluActionBlockBundle\Execution\ActionExecutionResult;
use PERSPEQTIVE\SuluActionBlockBundle\Registry\ServiceActionItemInterface;

class RedirecterServiceAction implements ServiceActionItemInterface
{
    public function __construct(private FormHandlerServiceInterface $formHandlerService) {}

    public function getIdentifier(): string
    {
        return self::class;
    }

    public function getTitle(): string
    {
        return 'Custom Form Handling with redirect';
    }

    public function execute(array $configuration = [], array $options = []): ActionExecutionResult
    {
        return new ActionExecutionResult('',
            $this->formHandlerService->handleForm($configuration) //Return a redirect Url
        );
    }

}