<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Fragment;

interface ActionBlockFragmentRendererInterface
{
    /**
     * Returns null when the action block cannot be rendered as a fragment and has to be rendered inline.
     */
    public function render(string $actionBlockName, array $options = []): ?string;
}
