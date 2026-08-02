<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlocks\ActionBlockTemplateGeneratorInterface;

class MockActionBlockTemplateGenerator implements ActionBlockTemplateGeneratorInterface
{
    public function __construct(public string $result = 'some content')
    {
    }

    public function generate(): string
    {
        return $this->result;
    }
}
