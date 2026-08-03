<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Twig;

use Twig\TwigFunction;

class RenderActionBlockExtension extends AbstractActionBlockRenderingExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'perspeqtive_render_action_block',
                [$this, 'renderActionBlock'],
                ['is_safe' => ['html']],
            ),
        ];
    }

    public function renderActionBlock(array $options = []): string
    {
        return $this->execute($options);
    }
}
