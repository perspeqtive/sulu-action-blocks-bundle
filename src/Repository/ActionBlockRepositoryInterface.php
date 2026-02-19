<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Repository;

use PERSPEQTIVE\SuluActionBlocksBundle\Entity\ActionBlock;

interface ActionBlockRepositoryInterface
{
    public function findById(int $id): ?ActionBlock;

    /**
     * @return ActionBlock[]
     */
    public function findAll();
}
