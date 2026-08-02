<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Structure\ActionBlocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlocks\ActionBlocksBuilder;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionBlockTemplateGenerator;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockCacheFileWriter;
use PHPUnit\Framework\TestCase;

class ActionBlocksBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $mockCacheFileWriter = new MockCacheFileWriter();
        $templateGenerator = new MockActionBlockTemplateGenerator('template content');
        $actionBlocksBuilder = new ActionBlocksBuilder(
            $templateGenerator,
            $mockCacheFileWriter,
        );
        $actionBlocksBuilder->build('chache-dir');

        self::assertEquals('action-blocks.xml', $mockCacheFileWriter->fileNames[0]);
        self::assertEquals('template content', $mockCacheFileWriter->content);
    }
}
