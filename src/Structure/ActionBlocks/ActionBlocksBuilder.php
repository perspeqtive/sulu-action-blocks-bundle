<?php

namespace PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Cache\FileWriter;

final readonly class ActionBlocksBuilder implements ActionBlocksBuilderInterface
{

    public function __construct(
        private ActionBlockTemplateGenerator $templateGenerator,
        private FileWriter $fileWriter,
    ) {}

    public function build(string $cacheDir): void
    {
        $content = $this->templateGenerator->generate();
        $this->fileWriter->writeContent($content, $cacheDir, 'action-blocks.xml');
    }

}