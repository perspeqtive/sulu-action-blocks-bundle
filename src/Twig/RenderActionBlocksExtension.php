<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Twig;

use Twig\TwigFunction;

class RenderActionBlocksExtension extends AbstractActionBlockRenderingExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'perspeqtive_render_action_blocks',
                [$this, 'renderActionBlocks'],
                ['is_safe' => ['html']],
            ),
        ];
    }

    public function renderActionBlocks(array $blocks = []): string
    {
        $result = '';
        foreach ($blocks as $block) {
            $result .= $this->execute($block);
        }

        return $result;
    }
}
