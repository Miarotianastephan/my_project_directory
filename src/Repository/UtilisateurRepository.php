<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    //    /**
    //     * @return Utilisateur[] Returns an array of Utilisateur objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function findOneByUserMatricule($value): ?Utilisateur
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.user_matricule = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findUserByMatricule(string $user_matricule): array{
        $entity_manager = $this->getEntityManager();
        $query = $entity_manager->createQuery(
            'select u.user_matricule
            from APP\Entity\Utilisateur u
            where u.user_matricule = :userMatricule'
        )->setParameter('userMatricule', $user_matricule);
        return $query->getResult();
    }
}
