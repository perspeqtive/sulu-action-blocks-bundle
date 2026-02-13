<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Content\Types;

use PERSPEQTIVE\SuluActionBlockBundle\Entity\ActionBlock;
use PERSPEQTIVE\SuluActionBlockBundle\Repository\ActionBlockRepository;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Component\Content\SimpleContentType;

/**
 * ContentType for selecting a single ActionBlock.
 */
class SingleActionBlockSelectionContentType extends SimpleContentType
{
    public function __construct(
        private readonly ActionBlockRepository $actionBlockRepository,
    ) {
        parent::__construct('single_action_block_selection', null);
    }

    /**
     * @return ActionBlock|null
     */
    public function getContentData(PropertyInterface $property)
    {
        $id = $property->getValue();
        if (!$id) {
            return null;
        }

        return $this->actionBlockRepository->findById($id);
    }

    /**
     * @return array<string, mixed>
     */
    public function getViewData(PropertyInterface $property): array
    {
        return [
            'id' => $property->getValue(),
        ];
    }
}
