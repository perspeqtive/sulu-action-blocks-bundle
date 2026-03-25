<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Resolver;

interface ResolverInterface
{
    public function resolve(array $data): array;
}