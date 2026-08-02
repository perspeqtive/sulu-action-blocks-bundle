<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Structure;

use PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlockStructureCacheWarmer;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionBlocksBuilder;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockEmptyActionBlocksBuilder;
use PHPUnit\Framework\TestCase;

class ActionBlockStructureCacheWarmerTest extends TestCase
{
    public function testWritesGeneratedTemplateIntoCacheDirectory(): void
    {
        $emptyActionBlocksTemplateBuilder = new MockEmptyActionBlocksBuilder();
        $actionBlocksTemplateBuilder = new MockActionBlocksBuilder();
        $warmer = new ActionBlockStructureCacheWarmer(
            $actionBlocksTemplateBuilder,
            $emptyActionBlocksTemplateBuilder,
        );
        $warmer->warmUp('some-dir');
        self::assertTrue($emptyActionBlocksTemplateBuilder->wasBuilt);
        self::assertTrue($actionBlocksTemplateBuilder->wasBuilt);
    }

    public function testIsOptional(): void {
        $emptyActionBlocksTemplateBuilder = new MockEmptyActionBlocksBuilder();
        $actionBlocksTemplateBuilder = new MockActionBlocksBuilder();
        $warmer = new ActionBlockStructureCacheWarmer(
            $actionBlocksTemplateBuilder,
            $emptyActionBlocksTemplateBuilder,
        );
        self::assertTrue($warmer->isOptional());
    }
}
