<?php

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationCollection;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationProviderInterface;

class MockActionBlockInformationProvider implements ActionBlockInformationProviderInterface
{

    public function __construct(public ActionBlockInformationCollection $result = new ActionBlockInformationCollection()) {}

    public function provide(): ActionBlockInformationCollection
    {
        return $this->result;
    }
}