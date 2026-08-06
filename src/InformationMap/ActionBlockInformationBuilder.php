<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\InformationMap;

use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ActionRegistryInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;
use RuntimeException;
use Symfony\Component\String\Slugger\SluggerInterface;

use function in_array;
use function sprintf;

readonly class ActionBlockInformationBuilder implements ActionBlockInformationProviderInterface
{
    public function __construct(
        private ActionRegistryInterface $actionRegistry,
        private SluggerInterface $slugger,
    ) {
    }

    public function provide(): ActionBlockInformationCollection
    {
        $informationCollection = new ActionBlockInformationCollection();

        /** @var ServiceActionItemInterface $action */
        foreach ($this->actionRegistry->getActions() as $action) {
            $blockname = $this->getBlockName($action);
            if (in_array($blockname, $informationCollection->getNames(), true) === true) {
                throw new RuntimeException(sprintf('Action block with name "%s" already exists', $blockname));
            }
            $informationCollection->add(new ActionBlockInformation(
                $blockname,
                $action->getTitle(),
                $action->getIdentifier(),
                empty($action->getConfigurationBlock()) === true,
            ));
        }
        $informationCollection->sort();

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
