<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Tests\Unit\Mocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionExecutionResult;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\CacheableActionItemInterface;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;

final class MockCacheableActionItem implements ServiceActionItemInterface, CacheableActionItemInterface
{
    public ?array $receivedOptions = null;

    public function __construct(
        public int $cacheTtl = 300,
        public string $html = '<h1>Cached</h1>',
        public ?string $configurationBlock = 'cacheable-configuration-block',
    ) {
    }

    public function getIdentifier(): string
    {
        return self::class;
    }

    public function getTitle(): string
    {
        return 'Cacheable Title';
    }

    public function getConfigurationBlock(): ?string
    {
        return $this->configurationBlock;
    }

    public function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }

    public function execute(array $options = []): ActionExecutionResult
    {
        $this->receivedOptions = $options;

        return new ActionExecutionResult($this->html);
    }
}
