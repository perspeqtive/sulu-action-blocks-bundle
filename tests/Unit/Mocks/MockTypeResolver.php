<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Resolver\TypeResolverInterface;

final class MockTypeResolver implements TypeResolverInterface
{
    public bool $supportsToReturn = false;

    public array $dataToReturn = [];

    public ?string $receivedType = null;

    public ?array $receivedData = null;

    public function supports(string $type): bool
    {
        $this->receivedType = $type;

        return $this->supportsToReturn;
    }

    public function resolve(array $data): array
    {
        $this->receivedData = $data;

        return $this->dataToReturn;
    }
}
