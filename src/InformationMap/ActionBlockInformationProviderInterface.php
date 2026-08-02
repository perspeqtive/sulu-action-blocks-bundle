<?php

namespace PERSPEQTIVE\SuluActionBlocksBundle\InformationMap;

interface ActionBlockInformationProviderInterface
{
    public function provide(): ActionBlockInformationCollection;
}