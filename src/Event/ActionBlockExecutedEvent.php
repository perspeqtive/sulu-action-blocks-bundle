<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Event;

use PERSPEQTIVE\SuluActionBlockBundle\Execution\ActionExecutionResult;
use Symfony\Contracts\EventDispatcher\Event;

class ActionBlockExecutedEvent extends Event
{
    public const string NAME = 'perspeqtive.action_block.executed';

    public function __construct(
        public readonly string $redirect
    ) {
    }
}
