<?php

namespace App\Repository;

use App\Entity\TransactionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TransactionType>
 */
class TransactionTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransactionType::class);
    }

    public function findTransactionByCode(string $trs_code): ?TransactionType
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.trs_code = :trs_code')
            ->setParameter('trs_code', $trs_code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findTransactionByModePaiement($mode){
        if($mode == 0){        // especes
            return $this->findTransactionByCode('CE-005');
        }elseif ($mode == 1) { // cheques
            return $this->findTransactionByCode('CE-004');
        }elseif ($mode == 2) { // subvention BFM
            return $this->findTransactionByCode('CE-006');
        }
    }

    //    /**
    //     * @return TransactionType[] Returns an array of TransactionType objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findTransactionByModePaiement($value): ?TransactionType
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
