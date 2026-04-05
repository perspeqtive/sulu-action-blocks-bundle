<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Configuration;

interface ConfigurationFactoryInterface
{
    public function create(array $data): Configuration;
}
