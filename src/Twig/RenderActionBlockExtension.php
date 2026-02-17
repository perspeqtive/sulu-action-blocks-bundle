<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Twig;

use PERSPEQTIVE\SuluActionBlockBundle\Execution\ActionBlockExecutor;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RenderActionBlockExtension extends AbstractExtension
{
    public function __construct(private readonly ActionBlockExecutor $executor)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'perspeqtive_render_action_block',
                [$this, 'renderActionBlock'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function renderActionBlock(int $actionBlockIdentifier, array $options = []): string
    {
        return $this->executor->execute($actionBlockIdentifier, $options);
    }
}
