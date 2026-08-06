<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Structure\ActionBlocks;

use DOMDocument;
use DOMXPath;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformation;
use PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlocks\ActionBlockTemplateGenerator;
use PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks\MockActionBlockInformationProvider;
use PHPUnit\Framework\TestCase;

class ActionBlockTemplateGeneratorTest extends TestCase
{
    private ActionBlockTemplateGenerator $generator;
    private MockActionBlockInformationProvider $informationProvider;

    protected function setUp(): void
    {
        $this->informationProvider = new MockActionBlockInformationProvider();
        $this->generator = new ActionBlockTemplateGenerator(
            __DIR__ . '/../../../../config/templates/action-blocks',
            $this->informationProvider,
        );
    }

    public function testGeneratesTemplateWithEmptyBlockTypeOnlyWhenNoActionsAreRegistered(): void
    {
        $xpath = $this->createXpath($this->generator->generate());

        self::assertSame('action-blocks', $xpath->evaluate('string(/t:template/t:key)'));
        self::assertSame(['action-block-empty'], $this->getTypeRefs($xpath));
    }

    public function testGeneratesTypeRefPerActionConfigurationBlock(): void
    {
        $this->informationProvider->result->add(new ActionBlockInformation('configuration-block', 'Title', 'configuration-block'));
        $this->informationProvider->result->add(new ActionBlockInformation('redirect-configuration-block', 'Title', 'redirect-configuration-block'));

        $xpath = $this->createXpath($this->generator->generate());

        self::assertSame(
            ['configuration-block', 'redirect-configuration-block'],
            $this->getTypeRefs($xpath),
        );
    }

    public function testGeneratedBlockUsesEmptyBlockAsDefaultType(): void
    {
        $xpath = $this->createXpath($this->generator->generate());

        self::assertSame('action-blocks', $xpath->evaluate('string(/t:template/t:properties/t:block/@name)'));
        self::assertSame(
            'action-block-empty',
            $xpath->evaluate('string(/t:template/t:properties/t:block/@default-type)'),
        );
    }

    public function testGeneratedBlockUsesFirstFoundBlockAsDefaultType(): void
    {
        $this->informationProvider->result->add(new ActionBlockInformation('configuration-block', 'Title', 'configuration-block'));
        $this->informationProvider->result->add(new ActionBlockInformation('redirect-configuration-block', 'Title', 'redirect-configuration-block'));

        $xpath = $this->createXpath($this->generator->generate());

        self::assertSame(
            'configuration-block',
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
