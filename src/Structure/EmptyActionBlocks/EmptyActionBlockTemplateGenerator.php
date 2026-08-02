<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Structure\EmptyActionBlocks;

use DOMDocument;
use DOMElement;
use DOMXPath;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformation;

readonly class EmptyActionBlockTemplateGenerator
{

    private const string TEMPLATE_NAMESPACE = 'http://schemas.sulu.io/template/template';
    public function __construct(
        private string $templatesPath,
    )
    {
    }

    public function generate(ActionBlockInformation $information): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;
        $document->load($this->templatesPath . '/empty-action-block.xml');

        $this->setNodeValue($document, 'key', $information->blockName);
        $this->setNodeValue($document, 'title', $information->title);

        return $document->saveXML();
    }

    public function setNodeValue(DOMDocument $document, string $fieldName, string $nodeValue): void
    {
        $xpath = new DOMXPath($document);
        $xpath->registerNamespace('template', self::TEMPLATE_NAMESPACE);

        $fields = $xpath->query('//template:' . $fieldName);
        /** @var DOMElement $keyField */
        $field = $fields[0];
        $field->nodeValue = $nodeValue;
    }

}
