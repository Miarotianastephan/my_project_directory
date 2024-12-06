<?php

namespace App\Repository;

use App\Entity\GroupeUtilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupeUtilisateur>
 */
class GroupeUtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupeUtilisateur::class);
    }

    /**
     * Recherche un groupe d'utilisateurs par son libellé.
     *
     * Cette méthode permet de rechercher un groupe d'utilisateurs dans la base de données en fonction de son libellé (`grp_libelle`).
     * Elle effectue une requête qui compare le champ `grp_libelle` à la valeur passée en paramètre (`$libelle`).
     * La méthode retourne un seul groupe d'utilisateurs correspondant au libellé spécifié, ou `null` si aucun groupe n'est trouvé.
     *
     * @param string $libelle Le libellé du groupe à rechercher.
     *
     * @return GroupeUtilisateur|null Le groupe d'utilisateurs correspondant au libellé, ou `null` si aucun groupe n'est trouvé.
     */
    public function findByLibelle($libelle): ?GroupeUtilisateur
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.grp_libelle = :val')
            ->setParameter('val', $libelle)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * Récupère la liste des groupes auxquels l'utilisateur n'est pas assigné, à l'exception du groupe actuel.
     *
     * Cette méthode permet de récupérer tous les groupes dans lesquels un utilisateur **n'est pas** affecté,
     * à l'exception de celui auquel l'utilisateur appartient actuellement. Cela permet de filtrer les groupes
     * en excluant le groupe d'appartenance de l'utilisateur passé en paramètre.
     *
     * @param int $currentGroupId L'ID du groupe actuel de l'utilisateur. Ce groupe sera exclu des résultats.
     *
     * @return GroupeUtilisateur[] Un tableau d'entités `GroupeUtilisateur` représentant les groupes
     *         dans lesquels l'utilisateur n'est pas assigné, à l'exception du groupe d'ID donné en paramètre.
     */
    public function findGroupsNotAssignedToUser(int $currentGroupId): array
    {
        // Création de la requête DQL pour récupérer tous les groupes sauf celui dont l'ID est passé en paramètre
        return $this->createQueryBuilder('g')
            ->where('g.id != :currentGroupId')
            ->setParameter('currentGroupId', $currentGroupId)
            ->getQuery()
            ->getResult();
    }
}
