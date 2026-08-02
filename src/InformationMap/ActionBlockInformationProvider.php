<?php

namespace PERSPEQTIVE\SuluActionBlocksBundle\InformationMap;

use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistry;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistryInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class ActionBlockInformationProvider implements ActionBlockInformationProviderInterface
{

    public function __construct(
        private ActionRegistryInterface $actionRegistry,
        private SluggerInterface        $slugger
    ) {}

    public function provide(): ActionBlockInformationCollection {

        $informationCollection = new ActionBlockInformationCollection();

        /** @var ServiceActionItemInterface $action */
        foreach ($this->actionRegistry->getActions() as $action) {
            $informationCollection->add(new ActionBlockInformation(
                $this->getBlockName($action),
                $action->getTitle(),
                $action->getIdentifier(),
            ));
        }

        return $informationCollection;
    }

    private function getBlockName(ServiceActionItemInterface $action): string
    {
        $blockName = $action->getConfigurationBlock();
        if (empty($blockName) === false) {
           return $blockName;
        }
        return 'action-blocks-' . $this->slugger->slug($action->getTitle())->lower()->toString();
    }

}