<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Structure\EmptyActionBlocks;

use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformation;

interface EmptyActionBlockTemplateGeneratorInterface
{
    public function generate(ActionBlockInformation $information): string;
}
