<?php

namespace App\Repository;

use App\Entity\BudgetType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BudgetType>
 */
class BudgetTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BudgetType::class);
    }

    /**
     * Fonction de recherche type de budget par libelle
     * @param string $libelle
     * @return BudgetType|null
     */
    public function findOneByLibelle(string $libelle): ?BudgetType
    {
        return $this->createQueryBuilder('b')
            ->Where('LOWER(b.libelle) = :val')
            ->setParameter('val', $libelle)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
