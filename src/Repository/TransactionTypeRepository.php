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

    public function findTransactionDepenseDirecte():array{
        $list_transaction_code = ['CE-007', 'CE-011'];
        return $this->createQueryBuilder('t')
            ->Where('t.trs_code = :dep_directe_paye_bfm')
            ->OrWhere('t.trs_code = :comptabilisation_frais_bancaire')
            ->setParameter('dep_directe_paye_bfm',$list_transaction_code[0])
            ->setParameter('comptabilisation_frais_bancaire',$list_transaction_code[1])
            ->getQuery()
            ->getResult();
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
