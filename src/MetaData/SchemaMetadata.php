<?php
declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\MetaData;

use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\FormMetadata;
use Sulu\Bundle\AdminBundle\Metadata\MetadataInterface;
use Sulu\Bundle\AdminBundle\Metadata\SchemaMetadata\ConstMetadata;
use Sulu\Bundle\AdminBundle\Metadata\SchemaMetadata\IfThenElseMetadata;
use Sulu\Bundle\AdminBundle\Metadata\SchemaMetadata\PropertyMetadata;
use Sulu\Bundle\AdminBundle\Metadata\SchemaMetadata\RefSchemaMetadata;
use Sulu\Bundle\AdminBundle\Metadata\SchemaMetadata\SchemaMetadataInterface;

class SchemaMetadata extends \Sulu\Bundle\AdminBundle\Metadata\SchemaMetadata\SchemaMetadata implements SchemaMetadataInterface
{

    /**
     * @var FormMetadata[]
     */
    private array $additionalMetaDatas = [];

    public function __construct(private readonly SchemaMetadataInterface $baseMetaData)
    {

    }

    public function addToActionProperty(FormMetadata $schemaMetadata): void
    {
        $this->additionalMetaDatas[] = $schemaMetadata;
    }


    public function toJsonSchema(): array
    {
        $schema = $this->baseMetaData->toJsonSchema();
        return $this->enhanceSchema($schema);
    }

    private function enhanceSchema(array $schema): array
    {
        foreach($this->additionalMetaDatas as $metaData) {
            $metaData = new IfThenElseMetadata(
                new \Sulu\Bundle\AdminBundle\Metadata\SchemaMetadata\SchemaMetadata([
                    new PropertyMetadata('type', true, new ConstMetadata($metaData->getName())),
                ]),
                new RefSchemaMetadata('#/definitions/' . $metaData->getName())
            );
            $schema['properties']['action']['items']['allOf'][] = $metaData->toJsonSchema();
        }

        return $schema;
    }

}