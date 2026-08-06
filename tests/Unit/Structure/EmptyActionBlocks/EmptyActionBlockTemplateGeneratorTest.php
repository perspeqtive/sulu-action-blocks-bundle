<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Structure\EmptyActionBlocks;

use DOMDocument;
use DOMXPath;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformation;
use PERSPEQTIVE\SuluActionBlocksBundle\Structure\EmptyActionBlocks\EmptyActionBlockTemplateGenerator;
use PHPUnit\Framework\TestCase;

class EmptyActionBlockTemplateGeneratorTest extends TestCase
{
    public function testGenerate(): void
    {
        $generator = new EmptyActionBlockTemplateGenerator(
            __DIR__ . '/../../../../config/templates/action-blocks',
        );

        $actionBlock = new ActionBlockInformation(
            'blockName',
            'Block Title',
            'Identifier',
        );

        $result = $generator->generate($actionBlock);
        $xpath = $this->createXpath($result);

        self::assertSame('blockName', $xpath->evaluate('string(/t:template/t:key)'));
        self::assertSame(
            'Block Title',
            $xpath->evaluate('string(/t:template/t:meta/t:title)'),
        );
    }

    private function createXpath(string $xml): DOMXPath
    {
        $document = new DOMDocument();
        self::assertTrue($document->loadXML($xml));

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('t', 'http://schemas.sulu.io/template/template');

        return $xpath;
    }
}
