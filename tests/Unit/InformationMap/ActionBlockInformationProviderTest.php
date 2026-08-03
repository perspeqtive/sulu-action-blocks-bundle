<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\InformationMap;

use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationProvider;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionRegistry;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItem;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItemWithEmptyConfigurationBlock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ActionBlockInformationProviderTest extends TestCase
{
    public function testProvideUsesProvidedBlockName(): void
    {
        $registry = new MockActionRegistry();
        $provider = new ActionBlockInformationProvider(
            $registry,
            new AsciiSlugger(),
        );

        $result = $provider->provide();

        self::assertSame(MockServiceActionItem::class, $result->first()->identifier);
        self::assertSame('Hello World Title', $result->first()->title);
        self::assertSame('mock-configuration-block', $result->first()->blockName);
        self::assertFalse($result->first()->isDefault);
    }

    public function testProvideBuildCustomBlockName(): void
    {
        $registry = new MockActionRegistry(new MockServiceActionItemWithEmptyConfigurationBlock());
        $provider = new ActionBlockInformationProvider(
            $registry,
            new AsciiSlugger(),
        );

        $result = $provider->provide();

        self::assertSame(MockServiceActionItemWithEmptyConfigurationBlock::class, $result->first()->identifier);
        self::assertSame('Empty Configuration Block Title', $result->first()->title);
        self::assertSame('action-blocks-empty-configuration-block-title', $result->first()->blockName);
        self::assertTrue($result->first()->isDefault);
    }
}
