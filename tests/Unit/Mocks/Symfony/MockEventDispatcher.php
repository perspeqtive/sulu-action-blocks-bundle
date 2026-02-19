<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\Symfony;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MockEventDispatcher implements EventDispatcherInterface
{
    public array $dispatchedEvent = [];

    public function addListener(string $eventName, callable $listener, int $priority = 0): void
    {
    }

    public function addSubscriber(EventSubscriberInterface $subscriber): void
    {
    }

    public function removeListener(string $eventName, callable $listener): void
    {
    }

    public function removeSubscriber(EventSubscriberInterface $subscriber): void
    {
    }

    public function dispatch(object $event, ?string $eventName = null): object
    {
        $this->dispatchedEvent[] = $event;

        return $event;
    }

    public function getListeners(?string $eventName = null): array
    {
        return [];
    }

    public function getListenerPriority(string $eventName, callable $listener): ?int
    {
        return null;
    }

    public function hasListeners(?string $eventName = null): bool
    {
        return false;
    }
}
