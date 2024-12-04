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

    public function findByEtatCode($etatCode): ?EtatDemande
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.etat_code = :val')
            ->setParameter('val', $etatCode)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
