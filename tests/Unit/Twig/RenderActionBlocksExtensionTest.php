<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Twig;

use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionBlockExecutor;
use PERSPEQTIVE\SuluActionBlocksBundle\Twig\RenderActionBlocksExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

class RenderActionBlocksExtensionTest extends TestCase
{
    private RenderActionBlocksExtension $extension;

    protected function setUp(): void
    {
        $executor = new MockActionBlockExecutor(' execution result');
        $this->extension = new RenderActionBlocksExtension($executor);
    }

    public function testGetFunctions(): void
    {
        $functions = $this->extension->getFunctions();
        self::assertCount(1, $functions);
        self::assertInstanceOf(TwigFunction::class, $functions[0]);
        self::assertEquals('perspeqtive_render_action_blocks', $functions[0]->getName());
    }

    public function testRenderActionBlockCallsExecutor(): void
    {
        $result = $this->extension->renderActionBlocks(
            [
                ['type' => 'action-block-123', 'foo' => 'bar'],
                ['type' => 'action-block-1234', 'foo' => 'bar'],
            ],
        );

        self::assertEquals(' execution result action-block-123 execution result action-block-1234', $result);
    }

    public function testRenderActionBlockCallsExecutorWithoutType(): void
    {
        $result = $this->extension->renderActionBlocks(
            [
                ['typo' => 'action-block-123', 'foo' => 'bar'],
                ['type' => 'action-block-123', 'foo' => 'bar'],
            ],
        );

        self::assertEquals(' execution result action-block-123', $result);
    }
}
