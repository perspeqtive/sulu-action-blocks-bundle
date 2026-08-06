<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Cache;

use PERSPEQTIVE\SuluActionBlocksBundle\Cache\ActionBlockCacheWarmer;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionBlockInformationCacheBuilder;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionBlocksBuilder;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockEmptyActionBlocksBuilder;
use PHPUnit\Framework\TestCase;

class ActionBlockCacheWarmerTest extends TestCase
{
    public function testWritesGeneratedTemplateIntoCacheDirectory(): void
    {
        $emptyActionBlocksTemplateBuilder = new MockEmptyActionBlocksBuilder();
        $actionBlocksTemplateBuilder = new MockActionBlocksBuilder();
        $actionBlockInfoCacheBuilder = new MockActionBlockInformationCacheBuilder();
        $warmer = new ActionBlockCacheWarmer(
            $actionBlocksTemplateBuilder,
            $emptyActionBlocksTemplateBuilder,
            $actionBlockInfoCacheBuilder,
        );
        $warmer->warmUp('some-dir');
        self::assertTrue($emptyActionBlocksTemplateBuilder->wasBuilt);
        self::assertTrue($actionBlocksTemplateBuilder->wasBuilt);
        self::assertTrue($actionBlockInfoCacheBuilder->wasBuilt);
    }

    public function testIsOptional(): void
    {
        $emptyActionBlocksTemplateBuilder = new MockEmptyActionBlocksBuilder();
        $actionBlocksTemplateBuilder = new MockActionBlocksBuilder();
        $actionBlockInfoCacheBuilder = new MockActionBlockInformationCacheBuilder();
        $warmer = new ActionBlockCacheWarmer(
            $actionBlocksTemplateBuilder,
            $emptyActionBlocksTemplateBuilder,
            $actionBlockInfoCacheBuilder,
        );
        self::assertFalse($warmer->isOptional());
    }
}
