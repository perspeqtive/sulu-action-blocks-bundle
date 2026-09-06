<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Fragment\ActionBlockFragmentRendererInterface;

final class MockActionBlockFragmentRenderer implements ActionBlockFragmentRendererInterface
{
    public ?string $receivedActionBlockName = null;
    public ?array $receivedOptions = null;

    public function __construct(public ?string $fragmentToReturn = '<esi:include src="/fragment" />')
    {
    }

    public function render(string $actionBlockName, array $options = []): ?string
    {
        $this->receivedActionBlockName = $actionBlockName;
        $this->receivedOptions = $options;

        return $this->fragmentToReturn;
    }
}
