<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Twig;

use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionBlockExecutorInterface;
use Twig\Extension\AbstractExtension;

abstract class AbstractActionBlockRenderingExtension extends AbstractExtension
{
    public function __construct(private readonly ActionBlockExecutorInterface $executor)
    {
    }

    protected function execute(array $data): string
    {
        $type = $data['type'] ?? null;
        if ($type === null) {
            return '';
        }
        unset($data['type']);

        return $this->executor->execute($type, $data);
    }
}
