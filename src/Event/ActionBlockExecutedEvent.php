<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ActionBlockExecutedEvent extends Event
{
    public function __construct(
        public readonly string $redirect,
    ) {
    }
}
