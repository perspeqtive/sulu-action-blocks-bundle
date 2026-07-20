<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Structure;

use DOMDocument;
use DOMXPath;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistry;
use PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlockTemplateGenerator;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItem;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItemForRedirect;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockServiceActionItemWithEmptyConfigurationBlock;
use PHPUnit\Framework\TestCase;

class ActionBlockTemplateGeneratorTest extends TestCase
{
    public function testGeneratesTemplateWithEmptyBlockTypeOnlyWhenNoActionsAreRegistered(): void
    {
        $generator = new ActionBlockTemplateGenerator(new ActionRegistry([]));

        $xpath = $this->createXpath($generator->generate());

        self::assertSame('action-block', $xpath->evaluate('string(/t:template/t:key)'));
        self::assertSame(['action-block-empty'], $this->getTypeRefs($xpath));
    }

    public function testGeneratesTypeRefPerActionConfigurationBlock(): void
    {
        $generator = new ActionBlockTemplateGenerator(new ActionRegistry([
            new MockServiceActionItem(),
            new MockServiceActionItemForRedirect(),
        ]));

        $xpath = $this->createXpath($generator->generate());

        self::assertSame(
            ['action-block-empty', 'mock-configuration-block', 'redirect-configuration-block'],
            $this->getTypeRefs($xpath),
        );
    }

    public function testSkipsDuplicateAndEmptyConfigurationBlocks(): void
    {
        $generator = new ActionBlockTemplateGenerator(new ActionRegistry([
            new MockServiceActionItem(),
            new MockServiceActionItem(),
            new MockServiceActionItemWithEmptyConfigurationBlock(),
        ]));

        $xpath = $this->createXpath($generator->generate());

        self::assertSame(['action-block-empty', 'mock-configuration-block'], $this->getTypeRefs($xpath));
    }

    public function testGeneratedBlockUsesEmptyBlockAsDefaultType(): void
    {
        $generator = new ActionBlockTemplateGenerator(new ActionRegistry([new MockServiceActionItem()]));

        $xpath = $this->createXpath($generator->generate());

        self::assertSame('action', $xpath->evaluate('string(/t:template/t:properties/t:block/@name)'));
        self::assertSame(
            'action-block-empty',
            $xpath->evaluate('string(/t:template/t:properties/t:block/@default-type)'),
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

    /**
     * @return string[]
     */
    private function getTypeRefs(DOMXPath $xpath): array
    {
        $refs = [];
        foreach ($xpath->query('/t:template/t:properties/t:block/t:types/t:type') ?: [] as $type) {
            $refs[] = $type->getAttribute('ref');
        }

        return $refs;
    }
}
