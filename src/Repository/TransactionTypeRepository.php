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


    /**
     * Ajoute un nouveau code de transaction.
     *
     * Cette méthode permet d'ajouter un nouveau code de transaction dans la base de données. Un objet `TransactionType` est créé,
     * avec le code, le libellé et la définition (optionnelle) fournis en paramètres. L'opération est effectuée au sein d'une transaction.
     * Si l'opération est réussie, elle retourne un message de succès. En cas d'échec, elle retourne une erreur détaillant la cause de l'échec.
     *
     * @param string $code Le code de la transaction.
     * @param string $libelle Le libellé de la transaction.
     * @param string|null $definition La définition de la transaction, optionnelle.
     *
     * @return JsonResponse Retourne une réponse JSON indiquant le succès ou l'échec de l'opération.
     *
     * En cas de succès, un message de confirmation est retourné. En cas d'erreur (par exemple, lors de la persistance de l'objet),
     * un message d'erreur détaillant l'exception est retourné.
     */
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

    /**
     * Recherche une transaction en fonction du mode de paiement spécifié.
     *
     * Cette méthode retourne une transaction particulière en fonction du mode de paiement. Selon le mode de paiement fourni en
     * paramètre (espèces, chèques ou subvention BFM), elle appelle la méthode `findTransactionByCode` avec un code spécifique
     * correspondant à ce mode.
     *
     * - Si le mode est 0, la méthode retourne la transaction pour les espèces (`CE-005`).
     * - Si le mode est 1, la méthode retourne la transaction pour les chèques (`CE-004`).
     * - Si le mode est 2, la méthode retourne la transaction pour la subvention BFM (`CE-006`).
     *
     * Si le mode de paiement ne correspond à aucune des valeurs spécifiées (0, 1, ou 2), la méthode ne retournera rien.
     *
     * @param int $mode Le mode de paiement (0 pour espèces, 1 pour chèques, 2 pour subvention BFM).
     *
     * @return mixed Retourne la transaction correspondante au code associé au mode de paiement spécifié,
     *               ou `null` si le mode n'est pas pris en charge.
     */
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

    /**
     * Recherche une transaction par son code unique.
     *
     * Cette méthode utilise le QueryBuilder de Doctrine pour effectuer une requête
     * sur l'entité TransactionType en se basant sur le champ `trs_code`.
     *
     * @param string $trs_code Le code unique de la transaction à rechercher.
     *
     * @return TransactionType|null Retourne l'objet TransactionType correspondant
     *                              si trouvé, ou null si aucune transaction ne correspond.
     *
     * @throws \Doctrine\ORM\NonUniqueResultException Si plusieurs résultats sont trouvés (ce qui ne devrait pas arriver
     *                                               si `trs_code` est unique dans la base de données).
     */
    public function findTransactionByCode(string $trs_code): ?TransactionType
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.trs_code = :trs_code')
            ->setParameter('trs_code', $trs_code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Recherche une transaction spécifique pour l'approvisionnement.
     *
     * Cette méthode utilise la méthode `findTransactionByCode` pour rechercher
     * une transaction ayant un code spécifique prédéfini (`CE-002`), correspondant
     * à une transaction d'approvisionnement.
     *
     * @return TransactionType|null Retourne l'objet TransactionType correspondant
     *                              si trouvé, ou null si aucune transaction ne correspond.
     */
    public function findTransactionForApprovision()
    {
        return $this->findTransactionByCode('CE-002');
    }

    /**
     * Recherche les transactions liées aux dépenses directes.
     *
     * Cette méthode effectue une requête pour récupérer toutes les transactions dont
     * le code correspond à l'une des valeurs prédéfinies dans la liste `$list_transaction_code`.
     * Elle utilise le QueryBuilder de Doctrine pour construire la requête.
     *
     * Codes transactionnels recherchés :
     * - `CE-001` : Encaissement subvention
     * - `CE-007` : Dépense directe payée par BFM
     * - `CE-010` : Encaissement des intérêts d'opérations
     * - `CE-011` : Comptabilisation des frais bancaires
     *
     * @return TransactionType[] Retourne un tableau d'objets TransactionType correspondant
     *                           aux transactions trouvées.
     *
     * @throws \Doctrine\ORM\Query\QueryException Si la requête est mal construite.
     */
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
