<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlocks;

interface ActionBlockTemplateGeneratorInterface
{
    public function generate(): string;
}
