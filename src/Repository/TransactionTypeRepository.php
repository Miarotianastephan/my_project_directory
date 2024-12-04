<?php

namespace App\Repository;

use App\Entity\TransactionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<TransactionType>
 */
class TransactionTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TransactionType::class);
    }


    public function ajoutCodeTransaction(string $code, string $libelle, ?string $definition): JsonResponse
    {
        $entityManager = $this->getEntityManager();
        $transactionType = new TransactionType();
        $transactionType->setTrsCode($code);
        $transactionType->setTrsLibelle($libelle);
        $transactionType->setTrsDefinition($definition);
        $entityManager->beginTransaction();
        try {
            $entityManager->persist($transactionType);
            $entityManager->flush();
            $entityManager->commit();
            return new JsonResponse([
                'success' => true,
                'message' => 'Le code de transaction a été bien ajouté'
            ]);
        } catch (\Exception $e) {
            $entityManager->rollback();
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function findTransactionByModePaiement($mode)
    {
        if ($mode == 0) {        // especes
            return $this->findTransactionByCode('CE-005');
        } elseif ($mode == 1) { // cheques
            return $this->findTransactionByCode('CE-004');
        } elseif ($mode == 2) { // subvention BFM
            return $this->findTransactionByCode('CE-006');
        }
    }

    public function findTransactionByCode(string $trs_code): ?TransactionType
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.trs_code = :trs_code')
            ->setParameter('trs_code', $trs_code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findTransactionForApprovision()
    {
        return $this->findTransactionByCode('CE-002');
    }


    public function findTransactionDepenseDirecte(): array
    {
        $list_transaction_code = ['CE-001', 'CE-007', 'CE-010', 'CE-011'];
        return $this->createQueryBuilder('t')
            ->Where('t.trs_code = :cencaissement_subvention')
            ->orWhere('t.trs_code = :dep_directe_paye_bfm')
            ->OrWhere('t.trs_code = :encaiseement_interet_operation')
            ->OrWhere('t.trs_code = :comptabilisation_frais_bancaire')
            ->setParameter('cencaissement_subvention', $list_transaction_code[0])
            ->setParameter('dep_directe_paye_bfm', $list_transaction_code[1])
            ->setParameter('encaiseement_interet_operation', $list_transaction_code[2])
            ->setParameter('comptabilisation_frais_bancaire', $list_transaction_code[3])
            ->getQuery()
            ->getResult();
    }

}
