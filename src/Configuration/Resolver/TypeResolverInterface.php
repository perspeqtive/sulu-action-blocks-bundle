<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Resolver;

interface TypeResolverInterface
{
    public function supports(string $type): bool;

    public function resolve(array $data): array;
}
