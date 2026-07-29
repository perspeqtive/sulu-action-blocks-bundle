<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Structure;

use DOMDocument;
use DOMElement;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistry;

use function in_array;

readonly class ActionBlockTemplateGenerator
{
    private const TEMPLATE_NAMESPACE = 'http://schemas.sulu.io/template/template';
    private const XSI_NAMESPACE = 'http://www.w3.org/2001/XMLSchema-instance';
    private const SCHEMA_LOCATION = 'http://schemas.sulu.io/template/template http://schemas.sulu.io/template/template-1.0.xsd';
    private const TEMPLATE_KEY = 'action-block';
    private const DEFAULT_TYPE = 'action-block-empty';

    public function __construct(private ActionRegistry $actionRegistry)
    {
    }

    public function generate(): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;

        $template = $document->createElementNS(self::TEMPLATE_NAMESPACE, 'template');
        $template->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', self::XSI_NAMESPACE);
        $template->setAttributeNS(self::XSI_NAMESPACE, 'xsi:schemaLocation', self::SCHEMA_LOCATION);
        $document->appendChild($template);

        $template->appendChild($this->createElement($document, 'key', self::TEMPLATE_KEY));

        $meta = $this->createElement($document, 'meta');
        $meta->appendChild($this->createElement($document, 'title', 'Action block'));
        $template->appendChild($meta);

        $properties = $this->createElement($document, 'properties');
        $properties->appendChild($this->createActionBlockProperty($document));
        $template->appendChild($properties);

        return (string) $document->saveXML();
    }

    private function createActionBlockProperty(DOMDocument $document): DOMElement
    {
        $block = $this->createElement($document, 'block');
        $block->setAttribute('name', 'action');
        $block->setAttribute('default-type', self::DEFAULT_TYPE);

        $block->appendChild($this->createAddButtonParams($document));
        $block->appendChild($this->createTypes($document));

        return $block;
    }

    private function createAddButtonParams(DOMDocument $document): DOMElement
    {
        $params = $this->createElement($document, 'params');

        $param = $this->createElement($document, 'param');
        $param->setAttribute('name', 'add_button_text');

        $meta = $this->createElement($document, 'meta');
        $meta->appendChild($this->createElement($document, 'title', 'Modul hinzufügen'));

        $param->appendChild($meta);
        $params->appendChild($param);

        return $params;
    }

    private function createTypes(DOMDocument $document): DOMElement
    {
        $types = $this->createElement($document, 'types');
        foreach ($this->collectBlockNames() as $blockName) {
            $type = $this->createElement($document, 'type');
            $type->setAttribute('ref', $blockName);
            $types->appendChild($type);
        }

        return $types;
    }

    /**
     * @return string[]
     */
    private function collectBlockNames(): array
    {
        $blockNames = [self::DEFAULT_TYPE];
        foreach ($this->actionRegistry->getActions() as $action) {
            $blockName = $action->getConfigurationBlock();
            if ($blockName === '' || in_array($blockName, $blockNames, true) === true) {
                continue;
            }
            $blockNames[] = $blockName;
        }

        return $blockNames;
    }

    private function createElement(DOMDocument $document, string $name, ?string $value = null): DOMElement
    {
        $element = $document->createElementNS(self::TEMPLATE_NAMESPACE, $name);
        if ($value !== null) {
            $element->textContent = $value;
        }

        return $element;
    }
}
