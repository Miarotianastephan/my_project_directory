<?php

namespace App\Repository;

use App\Entity\DetailTransactionCompte;
use App\Entity\TransactionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DetailTransactionCompte>
 */
class DetailTransactionCompteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailTransactionCompte::class);
    }

    public function findByTransaction(TransactionType $transactionType): array
    {
        return $this->createQueryBuilder('d')
            ->Where('d.transaction_type = :val')
            ->setParameter('val', $transactionType)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return DetailTransactionCompte[] Returns an array of DetailTransactionCompte objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?DetailTransactionCompte
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
