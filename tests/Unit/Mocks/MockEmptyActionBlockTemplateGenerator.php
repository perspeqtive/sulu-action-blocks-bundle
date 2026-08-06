<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformation;
use PERSPEQTIVE\SuluActionBlocksBundle\Structure\EmptyActionBlocks\EmptyActionBlockTemplateGeneratorInterface;

class MockEmptyActionBlockTemplateGenerator implements EmptyActionBlockTemplateGeneratorInterface
{
    public array $informationToBuild = [];

    public function __construct(public string $result = 'some content')
    {
    }

    public function generate(ActionBlockInformation $information): string
    {
        $this->informationToBuild[] = $information;

        return $this->result;
    }
}
