<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Execution;

use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionExecutionResult;
use PHPUnit\Framework\TestCase;

class ActionExecutionResultTest extends TestCase
{
    public function testResultValues(): void
    {
        $result = new ActionExecutionResult('html', '/redirect');
        self::assertEquals('html', $result->html);
        self::assertEquals('/redirect', $result->redirect);
    }

    public function testDefaultValues(): void
    {
        $result = new ActionExecutionResult();
        self::assertEquals('', $result->html);
        self::assertEquals('', $result->redirect);
    }
}
