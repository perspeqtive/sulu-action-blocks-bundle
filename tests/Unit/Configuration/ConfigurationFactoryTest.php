<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Configuration;

use InvalidArgumentException;
use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\ConfigurationFactory;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockResolver;
use PHPUnit\Framework\TestCase;

final class ConfigurationFactoryTest extends TestCase
{
    private MockResolver $resolver;
    private ConfigurationFactory $factory;

    protected function setUp(): void
    {
        $this->resolver = new MockResolver();
        $this->factory = new ConfigurationFactory($this->resolver);
    }

    public function testCreateThrowsExceptionIfNameIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Configuration value must have a name');

        $data = [
            ['value' => 'foo'],
        ];

        $this->factory->create($data);
    }

    public function testCreateReturnsConfigurationWithResolvedData(): void
    {
        $inputData = [
            ['name' => 'foo', 'value' => 'bar'],
            ['name' => 'baz', 'value' => 'qux'],
        ];

        $resolvedItem = ['name' => 'mocked', 'value' => 'value', 'resolved' => 'mocked_value'];
        $this->resolver->dataToReturn = $resolvedItem;

        $configuration = $this->factory->create($inputData);

        self::assertCount(2, $this->resolver->resolvedData);
        self::assertSame($inputData[0], $this->resolver->resolvedData[0]);
        self::assertSame($inputData[1], $this->resolver->resolvedData[1]);

        self::assertSame('mocked_value', $configuration->getResolved('foo'));
        self::assertSame('value', $configuration->get('foo'));
        self::assertSame('mocked_value', $configuration->getResolved('baz'));
        self::assertSame('value', $configuration->get('baz'));

        $this->expectException(InvalidArgumentException::class);
        $configuration->get('0');
    }
}
