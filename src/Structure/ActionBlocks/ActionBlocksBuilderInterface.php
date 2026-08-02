<?php

namespace PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlocks;

interface ActionBlocksBuilderInterface
{
    public function build(string $cacheDir): void;
}