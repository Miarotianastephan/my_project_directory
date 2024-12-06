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

    /**
     * Recherche un détail de transaction de compte en fonction du type de transaction fourni.
     *
     * Cette méthode utilise un constructeur de requêtes Doctrine pour rechercher une entité `DetailTransactionCompte`
     * associée à un `TransactionType` spécifique. Elle retourne soit un objet `DetailTransactionCompte` correspondant
     * à la recherche, soit `null` si aucun résultat n'est trouvé.
     *
     * @param TransactionType $transactionType L'objet représentant le type de transaction que l'on cherche.
     *
     * @return DetailTransactionCompte|null L'entité `DetailTransactionCompte` correspondante au type de transaction,
     * ou `null` si aucun détail de transaction n'est trouvé pour le type donné.
     *
     * @throws \Doctrine\ORM\ORMException Si une erreur survient lors de l'exécution de la requête (par exemple, problème de connexion).
     */
    public function findByTransaction(TransactionType $transactionType): ?DetailTransactionCompte
    {
        return $this->createQueryBuilder('d')
            ->Where('d.transaction_type = :val')
            ->setParameter('val', $transactionType)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Recherche un détail de transaction de compte en fonction du type de transaction et du type d'opération (débit ou crédit).
     *
     * Cette méthode utilise un constructeur de requêtes Doctrine pour rechercher une entité `DetailTransactionCompte`
     * en fonction du type de transaction fourni (`TransactionType`) et du type d'opération (débit ou crédit).
     * Elle retourne soit un objet `DetailTransactionCompte` correspondant à la recherche, soit `null` si aucun résultat n'est trouvé.
     *
     * @param TransactionType $transactionType L'objet représentant le type de transaction que l'on cherche.
     * @param int $isDebit (optionnel) Le type d'opération à rechercher :
     *        0 pour crédit, 1 pour débit. Par défaut, ce paramètre est 0 (crédit).
     *
     * @return DetailTransactionCompte|null L'entité `DetailTransactionCompte` correspondante au type de transaction et au type d'opération,
     * ou `null` si aucun détail de transaction n'est trouvé pour les critères donnés.
     *
     * @throws \Doctrine\ORM\ORMException Si une erreur survient lors de l'exécution de la requête (par exemple, problème de connexion).
     */
    public function findByTransactionWithTypeOperation(TransactionType $transactionType, $isDebit = 0): ?DetailTransactionCompte
    {
        return $this->createQueryBuilder('d')
            ->Where('d.transaction_type = :val')
            ->andWhere('d.isTrsDebit = :val2')
            // Définition du paramètre `transaction_type` pour la requête.
            ->setParameter('val', $transactionType)
            // Définition du paramètre `isTrsDebit` pour la requête. Par défaut, recherche pour les crédits (0).
            ->setParameter('val2', $isDebit)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // Pour avoir le compte credit du details 
    /**
     * Recherche tous les détails de transactions de type crédit associés à un type de transaction donné.
     *
     * Cette méthode utilise le QueryBuilder de Doctrine pour rechercher toutes les entités `DetailTransactionCompte` où le `transaction_type`
     * correspond à celui passé en paramètre, et où l'opération est un dédit (indiqué par `isTrsDebit = 1`).
     * Elle retourne un tableau de résultats contenant tous les détails de transaction correspondant à ces critères.
     * Si aucun résultat n'est trouvé, la méthode retournera un tableau vide.
     *
     * @param TransactionType $transactionType L'objet représentant le type de transaction pour lequel on veut rechercher les détails.
     *
     * @return DetailTransactionCompte[]|null Un tableau de détails de transaction (`DetailTransactionCompte`) correspondant au type de transaction
     *         et ayant une opération de crédit, ou `null` si aucun résultat n'est trouvé.
     *
     * @throws \Doctrine\ORM\ORMException Si une erreur survient lors de l'exécution de la requête (par exemple, problème de connexion).
     */
    public function findAllByTransaction(TransactionType $transactionType): ?array
    {
        return $this->createQueryBuilder('d')
            ->Where('d.transaction_type = :val')
            ->andWhere('d.isTrsDebit = 1')
            ->setParameter('val', $transactionType)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche le plan de compte associé à une transaction de type crédit pour un type de transaction donné.
     *
     * Cette méthode utilise le QueryBuilder de Doctrine pour rechercher un détail de transaction où le `transaction_type`
     * correspond à celui passé en paramètre, et où l'opération est un crédit (indiqué par `isTrsDebit = 0`).
     * Si un résultat est trouvé, la méthode retourne l'entité `PlanCompte` associée à cette transaction.
     * Si aucun résultat n'est trouvé, la méthode retourne `null`.
     *
     * @param TransactionType $transactionType L'objet représentant le type de transaction pour lequel on veut rechercher le plan de compte associé.
     *
     * @return PlanCompte|null L'entité `PlanCompte` associée à la transaction de type crédit pour le type de transaction donné,
     *         ou `null` si aucun résultat n'est trouvé.
     */
    public function findPlanCompte_CreditByTransaction(TransactionType $transactionType): ?PlanCompte
    {
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

    /**
     * Recherche les entités correspondant à un type de transaction et un plan de compte donnés.
     *
     * Cette méthode permet de récupérer les enregistrements d'une entité en fonction du type de transaction
     * et du plan de compte associés. Elle utilise Doctrine QueryBuilder pour construire une requête personnalisée.
     *
     * @param TransactionType $transactionType L'objet représentant le type de transaction à filtrer.
     * @param PlanCompte $planCompte L'objet représentant le plan de compte à filtrer.
     *
     * @return array|null Un tableau contenant les résultats correspondant aux critères spécifiés,
     *                    ou null si aucun résultat n'est trouvé.
     */
    public function findByTransactionAndPlanCompte(TransactionType $transactionType, PlanCompte $planCompte): ?array
    {
        $data = $this->createQueryBuilder('d')
            ->Where('d.transaction_type = :trs')
            ->andWhere('d.plan_compte = :plc')
            ->setParameter('trs', $transactionType)
            ->setParameter('plc', $planCompte)
            ->getQuery()
            ->getResult();
        return $data;
    }

}
