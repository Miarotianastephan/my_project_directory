<?php

namespace App\Repository;

use App\Entity\CompteMere;
use App\Entity\PlanCompte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CompteMere>
 */
class CompteMereRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompteMere::class);
    }

    //    /**
    //     * @return CompteMere[] Returns an array of CompteMere objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

       public function findByCptNumero($compteNumero): ?CompteMere
       {
           return $this->createQueryBuilder('c')
               ->andWhere('c.cpt_numero = :val')
               ->setParameter('val', $compteNumero)
               ->getQuery()
               ->getOneOrNullResult()
           ;
       }

       public function findByPlanCompte(PlanCompte $planCompte): ?CompteMere{
            return $this->createQueryBuilder('c')
                ->andWhere('c.planComptes = :val')
                ->setParameter('val', $planCompte)
                ->getQuery()
                ->getOneOrNullResult()
            ;
       }
}
