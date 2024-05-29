<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ItemCollection\CustomItemAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class CustomItemAttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomItemAttribute::class);
    }
}