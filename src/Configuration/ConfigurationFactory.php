<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Configuration;

use InvalidArgumentException;
use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Resolver\ResolverInterface;

readonly class ConfigurationFactory implements ConfigurationFactoryInterface
{
    public function __construct(private ResolverInterface $resolver)
    {
    }

    public function create(array $data): Configuration
    {
        foreach ($data as $index => $value) {
            if (empty($value['name'])) {
                throw new InvalidArgumentException('Configuration value must have a name');
            }
            unset($data[$index]);
            $data[$value['name']] = $this->resolver->resolve($value);
        }

        return new Configuration($data);
    }
}
