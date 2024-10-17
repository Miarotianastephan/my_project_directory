<?php

namespace App\Repository;

use App\Entity\DetailTransactionCompte;
use App\Entity\PlanCompte;
use App\Entity\TransactionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

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

    public function findByTransactionWithTypeOperation(TransactionType $transactionType, $isDebit = 0): ?DetailTransactionCompte
    {
        return $this->createQueryBuilder('d')
            ->Where('d.transaction_type = :val')
            ->andWhere('d.isTrsDebit = :val2')
            ->setParameter('val', $transactionType)
            ->setParameter('val2', $isDebit)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // Pour avoir le compte credit du details 

    public function findAllByTransaction(TransactionType $transactionType): ?array
    {
        return $this->createQueryBuilder('d')
            ->Where('d.transaction_type = :val')
            ->setParameter('val', $transactionType)
            ->getQuery()
            ->getResult();
    }

    public function findPlanCompte_CreditByTransaction(TransactionType $transactionType): ?PlanCompte
    {
        dump("transaction == " . $transactionType->getTrsLibelle());
        $data = $this->createQueryBuilder('d')
            ->Where('d.transaction_type = :val')
            ->andWhere('d.isTrsDebit = 0')
            ->setParameter('val', $transactionType)
            ->getQuery()
            ->getOneOrNullResult();
        if ($data) {
            return $data->getPlanCompte();
        } else {
            return null;
        }
    }

    public function findByTransactionAndPlanCompte(TransactionType $transactionType, PlanCompte $planCompte): ?array
    {
         $data = $this->createQueryBuilder('d')
            ->Where('d.transaction_type = :trs')
            ->andWhere('d.plan_compte = :plc')
            ->setParameter('trs', $transactionType)
            ->setParameter('plc', $planCompte)
            ->getQuery()
            ->getResult();
         dump($data);
        return $data;
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
