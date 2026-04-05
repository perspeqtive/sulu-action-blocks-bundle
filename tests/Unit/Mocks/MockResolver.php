<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Resolver\ResolverInterface;

final class MockResolver implements ResolverInterface
{
    /** @var array<string, mixed> */
    public array $dataToReturn = [];

    /** @var array<int, array<string, mixed>> */
    public array $resolvedData = [];

    public function resolve(array $data): array
    {
        $this->resolvedData[] = $data;

        return $this->dataToReturn;
    }
}
