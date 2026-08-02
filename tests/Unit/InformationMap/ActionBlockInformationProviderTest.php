<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\InformationMap;

use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationProvider;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionRegistry;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItem;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItemWithEmptyConfigurationBlock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;

use function iterator_to_array;

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
        $resultArray = iterator_to_array($result);
        self::assertSame(MockServiceActionItem::class, $resultArray[0]->identifier);
        self::assertSame('Hello World Title', $resultArray[0]->title);
        self::assertSame('mock-configuration-block', $resultArray[0]->blockName);
    }

    public function testProvideBuildCustomBlockName(): void
    {
        $registry = new MockActionRegistry(new MockServiceActionItemWithEmptyConfigurationBlock());
        $provider = new ActionBlockInformationProvider(
            $registry,
            new AsciiSlugger(),
        );

        $result = $provider->provide();
        $resultArray = iterator_to_array($result);
        self::assertSame(MockServiceActionItemWithEmptyConfigurationBlock::class, $resultArray[0]->identifier);
        self::assertSame('Empty Configuration Block Title', $resultArray[0]->title);
        self::assertSame('action-blocks-empty-configuration-block-title', $resultArray[0]->blockName);
    }
}
