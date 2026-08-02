<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Structure\EmptyActionBlocks;

use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformation;
use PERSPEQTIVE\SuluActionBlocksBundle\Structure\EmptyActionBlocks\EmptyActionBlocksBuilder;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionBlockInformationProvider;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockCacheFileWriter;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockEmptyActionBlockTemplateGenerator;
use PHPUnit\Framework\TestCase;

class EmptyActionBlocksBuilderTest extends TestCase
{
    private EmptyActionBlocksBuilder $emptyActionBlocksBuilder;
    private MockCacheFileWriter $mockCacheFileWriter;
    private MockActionBlockInformationProvider $actionBlockInformationProvider;
    private MockEmptyActionBlockTemplateGenerator $templateGenerator;

    protected function setUp(): void
    {
        $this->mockCacheFileWriter = new MockCacheFileWriter();
        $this->templateGenerator = new MockEmptyActionBlockTemplateGenerator('template content');
        $this->actionBlockInformationProvider = new MockActionBlockInformationProvider();
        $this->emptyActionBlocksBuilder = new EmptyActionBlocksBuilder(
            $this->actionBlockInformationProvider,
            $this->templateGenerator,
            $this->mockCacheFileWriter,
        );
    }

    public function testBuildNoFileIsWrittenOnEmptyActionBlocks(): void
    {
        $this->emptyActionBlocksBuilder->build('chache-dir');

        self::assertEquals([], $this->mockCacheFileWriter->fileNames);
        self::assertEquals('', $this->mockCacheFileWriter->content);
    }

    public function testBuildWithActionBlocks(): void
    {
        $block1 = new ActionBlockInformation('action-block-1', 'Action Block 1', 'identifier-1');
        $block2 = new ActionBlockInformation('action-block-2', 'Action Block 2', 'identifier-2');
        $this->actionBlockInformationProvider->result->add($block1);
        $this->actionBlockInformationProvider->result->add($block2);

        $this->emptyActionBlocksBuilder->build('chache-dir');

        self::assertSame([$block1, $block2], $this->templateGenerator->informationToBuild);
        self::assertEquals('action-block-1.xml', $this->mockCacheFileWriter->fileNames[0]);
        self::assertEquals('action-block-2.xml', $this->mockCacheFileWriter->fileNames[1]);
        self::assertEquals('template content', $this->mockCacheFileWriter->content);
    }
}
