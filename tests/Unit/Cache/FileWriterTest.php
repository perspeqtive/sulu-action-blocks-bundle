<?php

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Cache;

use PERSPEQTIVE\SuluActionBlocksBundle\Cache\FileWriter;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FileWriterTest extends TestCase
{

    public function testWriteContent(): void
    {
        $fileWriter = new FileWriter('/some/path');
        $fileWriter->writeContent('test', sys_get_temp_dir() . '/', 'filename.xml');

        self::assertFileExists(sys_get_temp_dir() . '/some/path/filename.xml');
    }

    public function testWriteContentWithFailToCreateDir(): void
    {
        $this->expectException(RuntimeException::class);

        $fileWriter = new FileWriter('/some/path');
        $fileWriter->writeContent('test', '/somewhere', 'filename.xml');
    }

    public function testWriteContentThrowsExceptionWhenFileCannotBeWritten(): void
    {
        $cacheDir = sys_get_temp_dir() . '/some/path';
        mkdir($cacheDir);

        chmod($cacheDir, 0555);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not write generated action block template');

        $fileWriter = new FileWriter('/some/path');
        $fileWriter->writeContent('test', sys_get_temp_dir() . '/', 'file.xml');
    }

    public function tearDown(): void
    {
        chmod(sys_get_temp_dir() . '/some/path', 0777);
        rmdir(sys_get_temp_dir() . '/some/path');
        rmdir(sys_get_temp_dir() . '/some');
        rmdir(sys_get_temp_dir());
    }


}