<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlocks;

use DOMDocument;
use DOMElement;
use DOMXPath;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationCollection;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationProvider;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationProviderInterface;

readonly class ActionBlockTemplateGenerator
{
    private const TEMPLATE_NAMESPACE = 'http://schemas.sulu.io/template/template';

    public function __construct(
        private string                                  $templatesPath,
        private ActionBlockInformationProviderInterface $actionBlockInformationProvider
    )
    {
    }

    public function generate(): string
    {
        $actionBlocksInformation = $this->actionBlockInformationProvider->provide();
        if ($actionBlocksInformation->isEmpty() === true) {
            return file_get_contents($this->templatesPath . '/action-blocks.xml');
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;
        $document->load($this->templatesPath . '/action-blocks.xml');

        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('template', self::TEMPLATE_NAMESPACE);

        $this->createTypes($xpath, $actionBlocksInformation);
        $this->addDefaultType($xpath, $actionBlocksInformation);

        return (string)$document->saveXML();
    }

    private function createTypes(DOMXPath $xpath, ActionBlockInformationCollection $actionBlocksInformation): void
    {
        $typesFields = $xpath->query('//template:types');

        $types = $typesFields[0];

        while ($types->firstChild !== null) {
            $types->removeChild($types->firstChild);
        }

        $document = $types->ownerDocument;;
        foreach ($actionBlocksInformation as $blockInformation) {
            $type = $document->createElementNS(self::TEMPLATE_NAMESPACE, 'type');
            $type->setAttribute('ref', $blockInformation->blockName);
            $types->appendChild($type);
        }

    }

    private function addDefaultType(DOMXPath $xpath, ActionBlockInformationCollection $actionBlocksInformation): void
    {
        $blockFields = $xpath->query('//template:block');
        $block = $blockFields[0];

        $block->setAttribute('default-type', $actionBlocksInformation->first()->blockName);
    }

}
