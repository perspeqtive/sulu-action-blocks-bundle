<?php

declare(strict_types=1);

namespace App\ActionBlocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Configuration;
use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionExecutionResult;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;

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

    public function getConfigurationBlock(): ?string
    {
        return null; //Null value means you do not configure a global block yourself. The title of self::getTitle() is presented to the user.
    }

    public function execute(array $options = []): ActionExecutionResult
    {
        return new ActionExecutionResult('',
            $this->formHandlerService->handleForm($options) //Return a redirect Url
        );
    }

}