<?php

namespace App\Repository;

use App\Entity\Exercice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Exercice>
 */
class ExerciceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercice::class);
    }
    
    public function getExerciceValide(\DateTime $date): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.exercice_date_debut > :date')
            ->andWhere('e.exercice_date_fin IS NULL')
            ->setParameter('date', $date, 'customdate')
            ->getQuery()
            ->getResult();
    }


    public function findMostRecentOpenExercice(): ?Exercice
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exercice_date_fin IS NULL')
            ->orderBy('e.exercice_date_debut', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
