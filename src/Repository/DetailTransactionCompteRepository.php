<?php

namespace App\Repository;

use App\Entity\DetailTransactionCompte;
use App\Entity\PlanCompte;
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

    public function findByTransaction(TransactionType $transactionType): ?DetailTransactionCompte
    {
        return $this->createQueryBuilder('d')
            ->Where('d.transaction_type = :val')
            ->setParameter('val', $transactionType)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPlanCompte_CreditByTransaction(TransactionType $transactionType): ?PlanCompte
    {
        dump("transaction == ".$transactionType->getTrsLibelle());
        $data = $this->createQueryBuilder('d')
            ->Where('d.transaction_type = :val')
            ->andWhere('d.isTrsDebit = 0')
            ->setParameter('val', $transactionType)
            ->getQuery()
            ->getOneOrNullResult();
        if ($data){return $data->getPlanCompte();}else{return null;}
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
