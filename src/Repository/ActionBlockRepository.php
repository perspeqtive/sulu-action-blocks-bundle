<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PERSPEQTIVE\SuluActionBlockBundle\Entity\ActionBlock;

/**
 * @extends ServiceEntityRepository<ActionBlock>
 *
 * @method ActionBlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActionBlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActionBlock[] findAll()
 * @method ActionBlock[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActionBlockRepository extends ServiceEntityRepository implements ActionBlockRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActionBlock::class);
    }

    public function findById(int $id): ?ActionBlock
    {
        return $this->find($id);
    }
}
