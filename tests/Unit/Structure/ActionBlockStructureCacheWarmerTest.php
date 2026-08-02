<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Structure;

use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistry;
use PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlocks\ActionBlockTemplateGenerator;
use PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlockStructureCacheWarmer;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionBlocksBuilder;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockEmptyActionBlocksBuilder;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItem;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use function file_get_contents;
use function file_put_contents;
use function is_dir;
use function is_file;
use function rmdir;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

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

}
