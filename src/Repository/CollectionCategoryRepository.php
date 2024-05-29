<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ItemCollection\CollectionCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class CollectionCategoryRepository extends ServiceEntityRepository implements \Countable
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CollectionCategory::class);
    }
}