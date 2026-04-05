<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Configuration;
use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\ConfigurationFactoryInterface;

class MockConfigurationFactory implements ConfigurationFactoryInterface
{
    public function __construct(public Configuration $configuration = new Configuration([]))
    {
    }

    public function create(array $data): Configuration
    {
        return $this->configuration;
    }
}
