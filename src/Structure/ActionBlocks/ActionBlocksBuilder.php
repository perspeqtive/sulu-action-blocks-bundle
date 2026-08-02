<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Structure\ActionBlocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Cache\CacheFileWriterInterface;

final readonly class ActionBlocksBuilder implements ActionBlocksBuilderInterface
{
    public function __construct(
        private ActionBlockTemplateGeneratorInterface $templateGenerator,
        private CacheFileWriterInterface $fileWriter,
    ) {
    }

    public function build(string $cacheDir): void
    {
        $content = $this->templateGenerator->generate();
        $this->fileWriter->writeContent($content, $cacheDir, 'action-blocks.xml');
    }
}
