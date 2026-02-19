<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Repository;

use PERSPEQTIVE\SuluActionBlockBundle\Entity\ActionBlock;

interface ActionBlockRepositoryInterface
{
    public function findById(int $id): ?ActionBlock;

    /**
     * @return ActionBlock[]
     */
    public function findAll();
}
