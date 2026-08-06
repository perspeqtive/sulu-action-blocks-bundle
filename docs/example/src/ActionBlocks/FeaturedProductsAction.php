<?php

declare(strict_types=1);

namespace App\ActionBlocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionExecutionResult;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;

final class FeaturedProductsAction implements ServiceActionItemInterface
{
    public function __construct(
        private readonly FeaturedProductServiceInterface $featuredProductService,
    ) {
    }

    public function getIdentifier(): string
    {
        return self::class;
    }

    public function getTitle(): string
    {
        return 'Featured products';
    }

    public function getConfigurationBlock(): ?string
    {
        return 'featured-products';
    }

    public function execute(array $options = []): ActionExecutionResult
    {
        return new ActionExecutionResult(
            html: $this->featuredProductService->getFeaturedProducts($options['product-ids'] ?? []),
        );
    }

}
