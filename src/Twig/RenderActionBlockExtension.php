<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Twig;

use PERSPEQTIVE\SuluActionBlockBundle\Execution\ActionBlockExecutor;
use Twig\Attribute\AsTwigFunction;
use Twig\Extension\AbstractExtension;

class RenderActionBlockExtension extends AbstractExtension
{
    public function __construct(private readonly ActionBlockExecutor $executor)
    {
    }

    #[AsTwigFunction('render_action_block')]
    public function renderActionBlock(string $actionBlockIdentifier, array $configuration = []): string
    {
        return $this->executor->execute($actionBlockIdentifier, $configuration);
    }
}
