<?php

namespace App\Repository;

use App\Entity\EtatDemande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EtatDemande>
 */
class EtatDemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtatDemande::class);
    }

    //    /**
    //     * @return EtatDemande[] Returns an array of EtatDemande objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

       public function findByEtatCode($etatCode): ?EtatDemande
       {
           return $this->createQueryBuilder('e')
               ->andWhere('e.etat_code = :val')
               ->setParameter('val', $etatCode)
               ->getQuery()
               ->getOneOrNullResult()
           ;
       }
}
