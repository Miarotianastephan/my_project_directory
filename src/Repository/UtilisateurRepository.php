<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * Recherche un utilisateur par son matricule.
     *
     * Cette méthode effectue une requête sur l'entité `Utilisateur` pour trouver un
     * utilisateur dont le matricule correspond à la valeur spécifiée.
     *
     * @param mixed $value Le matricule de l'utilisateur à rechercher.
     *
     * @return Utilisateur|null Retourne l'objet `Utilisateur` correspondant si trouvé,
     *                          ou `null` si aucun utilisateur ne correspond.
     *
     * @throws \Doctrine\ORM\NonUniqueResultException Si plusieurs résultats sont trouvés
     *                                               (ce qui ne devrait pas arriver si `user_matricule` est unique).
     */
    public function findOneByUserMatricule($value): ?Utilisateur
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.user_matricule = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Recherche un utilisateur par son matricule.
     *
     * Cette méthode utilise DQL (Doctrine Query Language) pour récupérer les utilisateurs
     * dont le matricule correspond à la valeur spécifiée. Seul le matricule est retourné.
     *
     * @param string $user_matricule Le matricule de l'utilisateur à rechercher.
     *
     * @return array Retourne un tableau contenant les matricules des utilisateurs
     *               correspondants. Chaque élément du tableau est une instance de
     *               `user_matricule`.
     *
     * @throws \Doctrine\ORM\Query\QueryException Si la requête est mal construite.
     */
    public function findUserByMatricule(string $user_matricule): array
    {
        $entity_manager = $this->getEntityManager();
        $query = $entity_manager->createQuery(
            'select u.user_matricule
            from APP\Entity\Utilisateur u
            where u.user_matricule = :userMatricule'
        )->setParameter('userMatricule', $user_matricule);
        return $query->getResult();
    }

    /**
     * Insère un nouvel utilisateur dans la base de données.
     *
     * Cette méthode effectue une insertion directe dans la table `ce_utilisateur`
     * en utilisant une requête SQL Oracle. Elle gère également une transaction pour
     * assurer la cohérence des données.
     *
     * @param string $user_matricule Le matricule de l'utilisateur à insérer.
     * @param int $groupeId L'ID du groupe auquel l'utilisateur appartient.
     * @param string $roles Les rôles attribués à l'utilisateur sous forme de chaîne.
     *
     * @return void
     *
     * @throws \Doctrine\DBAL\Exception Si une erreur survient lors de l'exécution de la requête.
     * @throws \Exception Si une erreur générale survient (par exemple, lors de la gestion de la transaction).
     *
     * Fonctionnement :
     * - Une connexion Doctrine est utilisée pour exécuter la requête SQL.
     * - Une séquence Oracle (`user_seq.NEXTVAL`) génère l'ID utilisateur.
     * - La méthode utilise une transaction pour garantir que toutes les modifications
     *   sont annulées si une erreur se produit.
     *
     * Étapes :
     * 1. Début de la transaction avec `$conn->beginTransaction()`.
     * 2. Préparation de la requête avec `$conn->prepare($sql)`.
     * 3. Liaison des valeurs aux paramètres de la requête.
     * 4. Exécution de la requête.
     * 5. Validation de la transaction avec `$conn->commit()`.
     * 6. En cas d'erreur, la transaction est annulée avec `$conn->rollBack()` et l'exception est relancée.
     */
    public function insertUtilisateur(string $user_matricule, int $groupeId, string $roles): void
    {
        // Requête SQL Oracle
        $sql = "INSERT INTO ce_utilisateur (user_id,user_matricule, grp_id, roles) 
                VALUES (user_seq.NEXTVAL,:user_matricule, :grp_id, :roles)";
        // Récupérer la connexion Doctrine
        $conn = $this->getEntityManager()->getConnection();
        // Démarrer la transaction
        $conn->beginTransaction();
        try {
            // Préparer et exécuter la requête SQL
            $stmt = $conn->prepare($sql);
            $stmt->bindValue('user_matricule', $user_matricule);
            $stmt->bindValue('grp_id', $groupeId);
            $stmt->bindValue('roles', $roles);
            $stmt->executeQuery();
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e; // Re-throw
        }
    }

    /**
     * Met à jour un utilisateur dans la base de données.
     *
     * Cette méthode utilise l'EntityManager de Doctrine pour persister les modifications
     * apportées à une entité `Utilisateur`. Elle gère également les exceptions pour fournir
     * un retour clair sur l'état de l'opération.
     *
     * @param Utilisateur $u L'objet `Utilisateur` à mettre à jour ou à créer.
     *
     * @return array Un tableau associatif contenant :
     * - `status` (bool) : Indique si l'opération a réussi.
     * - `message` (string) : Un message décrivant le résultat de l'opération.
     *
     * @throws \Exception Si une erreur se produit lors de la persistance ou de l'écriture en base de données.
     */
    public function updateUtilisateur(Utilisateur $u)
    {
        try {
            $em = $this->getEntityManager();
            $em->persist($u);
            $em->flush();
            return [
                "status" => true,
                "message" => sprintf('Utilisateur %s créer avec succès', $u->getUserMatricule()),
            ];
        } catch (\Exception $except) {
            return [
                "status" => false,
                "message" => $except->getMessage(),
            ];
        }
    }

}
