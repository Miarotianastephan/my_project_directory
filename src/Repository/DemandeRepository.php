<?php

namespace App\Repository;

use App\Entity\Demande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Demande>
 */
class DemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Demande::class);
    }

    /**
     * Fonction pour avoir toutes les demandes
     * @return array
     */

    public function findAll(): array
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findDemandeByCode($code_value): ?Demande
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.dm_code = :val')
            ->setParameter('val', $code_value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
