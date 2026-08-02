<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Twig;

use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionBlockExecutorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RenderActionBlockExtension extends AbstractExtension
{
    public function __construct(private readonly ActionBlockExecutorInterface $executor)
    {
    }

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
        $type = $options['type'] ?? null;
        if($type === null) {
            return '';
        }
        unset($options['type']);

        return $this->executor->execute($type, $options);
    }
}
