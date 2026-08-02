<?php

namespace PERSPEQTIVE\SuluActionBlocksBundle\Structure\EmptyActionBlocks;

interface EmptyActionBlocksBuilderInterface
{
    public function build(string $cacheDir): void;
}