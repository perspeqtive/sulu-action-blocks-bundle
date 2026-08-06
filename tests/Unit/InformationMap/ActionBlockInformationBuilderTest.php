<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\InformationMap;

use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationBuilder;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionRegistry;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItem;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItemForRedirect;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItemWithEmptyConfigurationBlock;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItemWithException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;

use function iterator_to_array;

class ActionBlockInformationBuilderTest extends TestCase
{
    public function testProvideUsesProvidedBlockName(): void
    {
        $registry = new MockActionRegistry();
        $builder = new ActionBlockInformationBuilder(
            $registry,
            new AsciiSlugger(),
        );

        $result = $builder->provide();

        self::assertSame(MockServiceActionItem::class, $result->first()->identifier);
        self::assertSame('Hello World Title', $result->first()->title);
        self::assertSame('mock-configuration-block', $result->first()->blockName);
        self::assertFalse($result->first()->needsGeneration);
    }

    public function testProvideBuildCustomBlockName(): void
    {
        $registry = new MockActionRegistry(new MockServiceActionItemWithEmptyConfigurationBlock());
        $builder = new ActionBlockInformationBuilder(
            $registry,
            new AsciiSlugger(),
        );

        $result = $builder->provide();

        self::assertSame(MockServiceActionItemWithEmptyConfigurationBlock::class, $result->first()->identifier);
        self::assertSame('Empty Configuration Block Title', $result->first()->title);
        self::assertSame('action-blocks-empty-configuration-block-title', $result->first()->blockName);
        self::assertTrue($result->first()->needsGeneration);
    }

    public function testProvideThrowsExceptionOnDuplicateBlockNames(): void
    {
        $registry = new MockActionRegistry(null);
        $registry->arrayResult = [
            new MockServiceActionItemWithEmptyConfigurationBlock(),
            new MockServiceActionItemWithEmptyConfigurationBlock(),
        ];
        $builder = new ActionBlockInformationBuilder(
            $registry,
            new AsciiSlugger(),
        );

        $this->expectException(\RuntimeException::class);
        $builder->provide();
    }

    public function testProvideOrdersResult(): void
    {
        $registry = new MockActionRegistry(null);
        $registry->arrayResult = [
            new MockServiceActionItemForRedirect(),
            new MockServiceActionItemWithException(),
            new MockServiceActionItemWithEmptyConfigurationBlock(),
        ];
        $builder = new ActionBlockInformationBuilder(
            $registry,
            new AsciiSlugger(),
        );

        $result = iterator_to_array($builder->provide());

        self::assertSame('Empty Configuration Block Title', $result[0]->title);
        self::assertSame('Exception Title', $result[1]->title);
        self::assertSame('Redirect Item', $result[2]->title);
    }
}
