<?php

namespace App\Repository;

use App\Entity\Banque;
use App\Entity\Chequier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chequier>
 */
class ChequierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chequier::class);
    }

    public function isExiste(string $numero, Banque $banque): bool
    {
        $count = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.chequier_numero_debut <= :numero')
            ->andWhere('c.chequier_numero_fin >= :numero')
            ->andWhere('c.banque = :banque')
            ->setParameter('numero', $numero, \Doctrine\DBAL\Types\Types::STRING)
            ->setParameter('banque', $banque)
            ->getQuery()
            ->getSingleScalarResult();

        // Retourne true si au moins un résultat existe
        return $count !== null && $count > 0;
    }


    //    /**
    //     * @return Chequier[] Returns an array of Chequier objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Chequier
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
