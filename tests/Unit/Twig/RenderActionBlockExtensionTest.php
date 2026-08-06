<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Twig;

use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionBlockExecutor;
use PERSPEQTIVE\SuluActionBlocksBundle\Twig\RenderActionBlockExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

class RenderActionBlockExtensionTest extends TestCase
{
    private RenderActionBlockExtension $extension;

    protected function setUp(): void
    {
        $executor = new MockActionBlockExecutor('execution result');
        $this->extension = new RenderActionBlockExtension($executor);
    }

    public function testGetFunctions(): void
    {
        $functions = $this->extension->getFunctions();
        self::assertCount(1, $functions);
        self::assertInstanceOf(TwigFunction::class, $functions[0]);
        self::assertEquals('perspeqtive_render_action_block', $functions[0]->getName());
    }

    public function testRenderActionBlockCallsExecutor(): void
    {
        $result = $this->extension->renderActionBlock(['type' => 'action-block-123', 'foo' => 'bar']);

        self::assertEquals('execution result action-block-123', $result);
    }

    public function testRenderActionBlockCallsExecutorWithoutType(): void
    {
        $result = $this->extension->renderActionBlock(['typo' => 'action-block-123', 'foo' => 'bar']);

        self::assertEquals('', $result);
    }
}
