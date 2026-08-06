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

    private const HASH_LENGTH = 6;

    private const MAX_KEY_LENGTH = 31;

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
        $hash = substr(hash('xxh3', $blockName . ':' . $action->getIdentifier()), 0, self::HASH_LENGTH);

        $titleLength = self::MAX_KEY_LENGTH - self::HASH_LENGTH - 4;
        return 'ab-' . $this->slugger->slug($action->getTitle())->lower()->slice(0, $titleLength)->toString() . '-' . $hash;
    }
}
