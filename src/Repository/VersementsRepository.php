<?php

namespace App\Repository;

use App\Entity\Versements;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Versements>
 */
class VersementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Versements::class);
    }

    public function persistVersement(EntityManager $em, 
                                    $nom_remettant, 
                                    $vrsm_date,
                                    $adresse,
                                    $vrsm_montant,
                                    $demande_obj,
                                    $utilisateur_obj,
                                    $vrsm_motif)
    {
        $vrsm = new Versements($nom_remettant,$vrsm_date,$adresse,$vrsm_montant,$demande_obj,$utilisateur_obj,$vrsm_motif);
        $em->persist($vrsm);
        $em->flush();
        return $vrsm;
    }
}
