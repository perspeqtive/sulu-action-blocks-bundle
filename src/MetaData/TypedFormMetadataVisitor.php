<?php
declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\MetaData;

use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;
use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\FieldMetadata;
use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\FormMetadata;
use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\TypedFormMetadata;
use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\TypedFormMetadataVisitorInterface;

class TypedFormMetadataVisitor implements TypedFormMetadataVisitorInterface
{
    private ?FieldMetadata $cachedActionMetaData = null;

    /**
     * @param iterable<ServiceActionItemInterface> $services
     */
    public function __construct(private readonly iterable $services) {}

    /**
     * @inheritDoc
     */
    public function visitTypedFormMetadata(TypedFormMetadata $formMetadata, string $key, string $locale, array $metadataOptions = []): void
    {
        if ($key !== 'block') {
            return;
        }

        $forms = $formMetadata->getForms();
        if(isset($forms['action-block']) === false) {
            return;
        }

        $this->rebuildActionBlockMetaData($formMetadata, $forms);
    }

    /**
     * @param FormMetadata[] $forms
     * @param TypedFormMetadata $formMetadata
     * @return void
     */
    private function rebuildActionBlockMetaData(TypedFormMetadata $formMetadata, array $forms): void
    {
        /** @var FieldMetadata[] $fieldMetaDatas */
        $fieldMetaDatas = $forms['action-block']->getItems();
        if (isset($fieldMetaDatas['action']) === false) {
            return;
        }

        $fieldMetaDatas['action'] = $this->enhanceActionWithServiceBlocks($fieldMetaDatas['action'], $forms);

        $forms['action-block']->setItems($fieldMetaDatas);
        $formMetadata->addForm('action-block', $forms['action-block']);
    }

    /**
     * @param FieldMetadata $actionMetaData
     * @param FormMetadata[] $forms
     * @return FieldMetadata
     */
    private function enhanceActionWithServiceBlocks(FieldMetadata $actionMetaData, array $forms): FieldMetadata
    {
        if($this->cachedActionMetaData === null) {
            foreach ($this->services as $service) {
                $block = $service->getConfigurationBlock();
                if ($this->isValidBlock($block, $forms) === false) {
                    continue;
                }
                $actionMetaData->addType($forms[$block]);
            }
            $this->cachedActionMetaData = $actionMetaData;
        }
        return $this->cachedActionMetaData;
    }

    private function isValidBlock(string $block, array $forms): bool
    {
        if (empty($block)) {
            return false;
        }
        return isset($forms[$block]) === true;
    }
}