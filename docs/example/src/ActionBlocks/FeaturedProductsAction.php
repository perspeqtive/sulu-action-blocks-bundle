<?php

declare(strict_types=1);

namespace App\ActionBlocks;

use PERSPEQTIVE\SuluActionBlocksBundle\Configuration\Configuration;
use PERSPEQTIVE\SuluActionBlocksBundle\Execution\ActionExecutionResult;
use PERSPEQTIVE\SuluActionBlocksBundle\Registry\ServiceActionItemInterface;

class FeaturedProductsAction implements ServiceActionItemInterface
{

    public function __construct(private FeaturedProductServiceInterface $featuredProductService) {}

    public function getIdentifier(): string
    {
        return self::class;
    }

    public function getTitle(): string
    {
        return 'Featured Products'; //Will be a selectable option in the Action Block admin panel
    }

    public function execute(Configuration $configuration, array $options = []): ActionExecutionResult
    {
        return new ActionExecutionResult($this->featuredProductService->getFeaturedProducts($configuration));
    }

}