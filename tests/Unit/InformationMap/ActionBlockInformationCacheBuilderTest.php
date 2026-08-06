<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\InformationMap;

use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationBuilder;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationCacheBuilder;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionRegistry;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockCacheFileWriter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\String\Slugger\AsciiSlugger;

final class ActionBlockInformationCacheBuilderTest extends TestCase
{
    private MockCacheFileWriter $cacheFileWriter;
    private ActionBlockInformationCacheBuilder $cacheBuilder;

    protected function setUp(): void
    {
        $this->cacheFileWriter = new MockCacheFileWriter();
        $informationBuilder = new ActionBlockInformationBuilder(
            new MockActionRegistry(),
            new AsciiSlugger(),
        );
        $this->cacheBuilder = new ActionBlockInformationCacheBuilder(
            $informationBuilder,
            $this->cacheFileWriter,
        );
    }

    public function testBuildWritesProvidedInformationAsJsonCacheFile(): void
    {
        $this->cacheBuilder->build('/cache');

        self::assertSame(
            '[{"blockName":"mock-configuration-block","title":"Hello World Title",'
            . '"identifier":"PERSPEQTIVE\\\\SuluActionBlocksBundle\\\\Tests\\\\Unit\\\\Mocks\\\\MockServiceActionItem",'
            . '"needsGeneration":false}]',
            $this->cacheFileWriter->content,
        );
        self::assertSame([ActionBlockInformationCacheBuilder::CACHE_FILE_NAME], $this->cacheFileWriter->fileNames);
        self::assertSame(['/cache'], $this->cacheFileWriter->cacheDirectories);
    }
}
