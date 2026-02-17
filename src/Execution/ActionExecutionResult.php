<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Execution;

readonly class ActionExecutionResult
{
    public function __construct(
        public string $html = '',
        public string $redirect = '',
    ) {
    }
}
