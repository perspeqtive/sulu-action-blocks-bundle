<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\Symfony;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentRendererInterface;

final class MockFragmentRenderer implements FragmentRendererInterface
{
    public ?string $receivedUri = null;

    public function __construct(
        private string $name = 'esi',
        public string $content = '<esi:include src="/fragment" />',
    ) {
    }

    public function render(string|ControllerReference $uri, Request $request, array $options = []): Response
    {
        $this->receivedUri = (string) $uri;

        return new Response($this->content);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
