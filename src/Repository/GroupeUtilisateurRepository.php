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

    //    /**
    //     * @return GroupeUtilisateur[] Returns an array of GroupeUtilisateur objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

       public function findByLibelle($libelle): ?GroupeUtilisateur
       {
           return $this->createQueryBuilder('g')
               ->andWhere('g.grp_libelle = :val')
               ->setParameter('val', $libelle)
               ->getQuery()
               ->getOneOrNullResult()
           ;
       }
    
        /**
     * Récupère la liste des groupes où l'utilisateur n'est pas concerné.
     *
     * @param int $currentGroupId L'ID du groupe actuel de l'utilisateur
     * @return GroupeUtilisateur[] Retourne un tableau d'entités GroupeUtilisateur
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
