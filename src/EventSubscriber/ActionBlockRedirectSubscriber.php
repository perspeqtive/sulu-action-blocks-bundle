<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\EventSubscriber;

use PERSPEQTIVE\SuluActionBlockBundle\Event\ActionBlockExecutedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ActionBlockRedirectSubscriber implements EventSubscriberInterface
{
    private const string ATTR_REDIRECT_URL = '_perspeqtive_action_block_redirect_url';

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ActionBlockExecutedEvent::class => 'onActionBlockExecuted',
            KernelEvents::RESPONSE => ['onKernelResponse', -64],
        ];
    }

    public function onActionBlockExecuted(ActionBlockExecutedEvent $event): void
    {
        $redirect = $event->redirect;
        if (empty($redirect)) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        $request->attributes->set(self::ATTR_REDIRECT_URL, $redirect);
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if ($this->shouldRedirect($event) === false) {
            return;
        }

        $request = $event->getRequest();

        $redirectUrl = $request->attributes->get(self::ATTR_REDIRECT_URL);
        if (empty($redirectUrl) === true) {
            return;
        }

        $event->setResponse(new RedirectResponse((string) $redirectUrl));
        $request->attributes->remove(self::ATTR_REDIRECT_URL);
    }

    private function shouldRedirect(ResponseEvent $event): bool
    {
        if (!$event->isMainRequest()) {
            return false;
        }
        if ($event->getResponse() instanceof RedirectResponse) {
            return false;
        }

        return true;
    }
}
