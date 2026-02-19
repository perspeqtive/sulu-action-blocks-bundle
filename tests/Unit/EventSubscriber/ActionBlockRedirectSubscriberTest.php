<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Tests\Unit\EventSubscriber;

use PERSPEQTIVE\SuluActionBlockBundle\Event\ActionBlockExecutedEvent;
use PERSPEQTIVE\SuluActionBlockBundle\EventSubscriber\ActionBlockRedirectSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ActionBlockRedirectSubscriberTest extends TestCase
{
    private RequestStack $requestStack;
    private ActionBlockRedirectSubscriber $subscriber;

    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();
        $this->subscriber = new ActionBlockRedirectSubscriber($this->requestStack);
    }

    public function testOnActionBlockExecutedSetsAttribute(): void
    {
        $request = new Request();
        $this->requestStack->push($request);

        $event = new ActionBlockExecutedEvent('/redirect-url');
        $this->subscriber->onActionBlockExecuted($event);

        self::assertEquals('/redirect-url', $request->attributes->get('_perspeqtive_action_block_redirect_url'));
    }

    public function testOnKernelResponseRedirects(): void
    {
        $request = new Request();
        $request->attributes->set('_perspeqtive_action_block_redirect_url', '/redirect-url');

        $kernel = $this->createMock(HttpKernelInterface::class);
        $response = new Response();
        $event = new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response);

        $this->subscriber->onKernelResponse($event);

        self::assertInstanceOf(RedirectResponse::class, $event->getResponse());
        self::assertEquals('/redirect-url', $event->getResponse()->getTargetUrl());
        self::assertFalse($request->attributes->has('_perspeqtive_action_block_redirect_url'));
    }

    public function testOnKernelResponseDoesNotRedirectIfNoAttribute(): void
    {
        $request = new Request();
        $kernel = $this->createMock(HttpKernelInterface::class);
        $response = new Response();
        $event = new ResponseEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $response);

        $this->subscriber->onKernelResponse($event);

        self::assertSame($response, $event->getResponse());
    }

    public function testOnKernelResponseDoesNotRedirectIfSubRequest(): void
    {
        $request = new Request();
        $request->attributes->set('_perspeqtive_action_block_redirect_url', '/redirect-url');

        $kernel = $this->createMock(HttpKernelInterface::class);
        $response = new Response();
        $event = new ResponseEvent($kernel, $request, HttpKernelInterface::SUB_REQUEST, $response);

        $this->subscriber->onKernelResponse($event);

        self::assertSame($response, $event->getResponse());
    }
}
