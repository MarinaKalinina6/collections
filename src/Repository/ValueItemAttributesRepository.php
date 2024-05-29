<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Item\ValueItemAttributes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ValueItemAttributesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ValueItemAttributes::class);
    }
}