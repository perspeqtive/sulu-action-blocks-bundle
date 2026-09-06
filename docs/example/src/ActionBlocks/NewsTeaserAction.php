<?php

declare(strict_types=1);

namespace App\ActionBlocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionExecutionResult;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\CacheableActionItemInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;

final class NewsTeaserAction implements ServiceActionItemInterface, CacheableActionItemInterface
{
    private const DEFAULT_LIMIT = 3;

    public function __construct(
        private readonly NewsTeaserRendererInterface $newsTeaserRenderer,
    ) {
    }

    public function getIdentifier(): string
    {
        return self::class;
    }

    public function getTitle(): string
    {
        return 'News teaser';
    }

    public function getConfigurationBlock(): ?string
    {
        return 'news-teaser';
    }

    public function getCacheTtl(): int
    {
        return 300;
    }

    public function execute(array $options = []): ActionExecutionResult
    {
        return new ActionExecutionResult(
            html: $this->newsTeaserRenderer->render(
                (string) ($options['category'] ?? ''),
                (int) ($options['limit'] ?? self::DEFAULT_LIMIT),
            ),
        );
    }
}
