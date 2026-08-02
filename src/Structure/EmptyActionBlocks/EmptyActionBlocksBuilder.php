<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Structure\EmptyActionBlocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Cache\FileWriter;
use PERSPEQTIVE\SuluActionBlocksBundle\InformationMap\ActionBlockInformationProviderInterface;

readonly class EmptyActionBlocksBuilder implements EmptyActionBlocksBuilderInterface
{
    public function __construct(
        private ActionBlockInformationProviderInterface $actionBlockInformationProvider,
        private EmptyActionBlockTemplateGenerator $emptyActionBlockTemplateGenerator,
        private FileWriter $fileWriter,
    ) {
    }

    public function build(string $cacheDir): void
    {
        $actionBlocksInformation = $this->actionBlockInformationProvider->provide();
        if ($actionBlocksInformation->isEmpty() === true) {
            return;
        }

        foreach ($actionBlocksInformation as $information) {
            $content = $this->emptyActionBlockTemplateGenerator->generate($information);
            $this->fileWriter->writeContent($content, $cacheDir, $information->blockName . '.xml');
        }
    }
}
