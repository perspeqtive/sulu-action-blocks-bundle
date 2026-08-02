<?php

namespace PERSPEQTIVE\SuluActionBlocksBundle\InformationMap;

readonly class ActionBlockInformation
{

    public function __construct(
        public string $blockName,
        public string $title,
        public string $identifier,
    ) {

    }

}