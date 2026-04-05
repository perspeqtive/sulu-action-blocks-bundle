<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Configuration;

use InvalidArgumentException;

readonly class Configuration
{
    public function __construct(private array $data)
    {
    }

    public function get(string $name): ?string
    {
        if (!isset($this->data[$name])) {
            throw new InvalidArgumentException("Configuration key '$name' not found");
        }

        return $this->data[$name]['value'];
    }

    public function getResolved(string $name): ?string
    {
        if (!isset($this->data[$name])) {
            throw new InvalidArgumentException("Configuration key '$name' not found");
        }

        return $this->data[$name]['resolved'] ?? $this->get($name);
    }
}
