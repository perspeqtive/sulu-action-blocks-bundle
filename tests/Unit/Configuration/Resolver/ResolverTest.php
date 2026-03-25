<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Configuration\Resolver;

use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Resolver\Resolver;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockTypeResolver;
use PHPUnit\Framework\TestCase;

final class ResolverTest extends TestCase
{

    private MockTypeResolver $mockTypeResolver;

    private Resolver $resolver;

    protected function setUp(): void
    {
        $this->mockTypeResolver = new MockTypeResolver();
        $this->resolver = new Resolver([$this->mockTypeResolver]);
    }

    public function testResolveReturnsOriginalDataWhenNoTypeProvided(): void
    {
        $data = ['foo' => 'bar'];

        $result = $this->resolver->resolve($data);

        $this->assertSame($data, $result);
        $this->assertSame('none', $this->mockTypeResolver->receivedType);
    }

    public function testResolveReturnsOriginalDataWhenNoResolverSupportsCurrentType(): void
    {
        $data = ['type' => 'unsupported', 'foo' => 'bar'];
        $this->mockTypeResolver->supportsToReturn = false;

        $result = $this->resolver->resolve($data);

        $this->assertSame($data, $result);
        $this->assertSame('unsupported', $this->mockTypeResolver->receivedType);
    }

    public function testResolveReturnsResolvedDataWhenResolverSupportsCurrentType(): void
    {
        $data = ['type' => 'supported', 'foo' => 'bar'];
        $resolvedData = ['type' => 'supported', 'foo' => 'resolved'];

        $this->mockTypeResolver->supportsToReturn = true;
        $this->mockTypeResolver->dataToReturn = $resolvedData;

        $result = $this->resolver->resolve($data);

        $this->assertSame($resolvedData, $result);
        $this->assertSame('supported', $this->mockTypeResolver->receivedType);
        $this->assertSame($data, $this->mockTypeResolver->receivedData);
    }

    public function testResolveStopsAtFirstSupportingResolver(): void
    {
        $secondMockResolver = new MockTypeResolver();
        $resolver = new Resolver([$this->mockTypeResolver, $secondMockResolver]);

        $data = ['type' => 'supported', 'foo' => 'bar'];
        $resolvedData = ['type' => 'supported', 'foo' => 'resolved'];

        $this->mockTypeResolver->supportsToReturn = true;
        $this->mockTypeResolver->dataToReturn = $resolvedData;

        $secondMockResolver->supportsToReturn = true;
        $secondMockResolver->dataToReturn = ['something' => 'else'];

        $result = $resolver->resolve($data);

        $this->assertSame($resolvedData, $result);
        $this->assertNull($secondMockResolver->receivedType);
    }

}
