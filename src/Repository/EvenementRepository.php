<?php

namespace App\Repository;

use App\Entity\CompteMere;
use App\Entity\Evenement;
use App\Entity\Exercice;
use App\Entity\TransactionType;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenement>
 */
class EvenementRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    public function findByEvnReference($reference): ?Evenement
    {
        return $this->createQueryBuilder('e')
            ->where('e.evn_reference = :val')
            ->setParameter('val', $reference)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function persistEvenement(
    EntityManager $em,
    TransactionType $trsType,
    Utilisateur $utilisateur,
    Exercice $exercice,
    $codeEntite,
    $montant,
    $reference,
    $dateOperation
    ){
        $evn = new Evenement($trsType, $utilisateur, $exercice, $codeEntite,$montant,$reference,$dateOperation);
        $em->persist($evn);
        $em->flush();
        return $evn;
    }

    // Pour avoir un liste d'Evenement par utilisateur qui l'as effectué
    public function findEvnByResponsable(Utilisateur $responsable){
        return $this->createQueryBuilder('e')
            ->andWhere('e.evn_responsable = :val')
            ->setParameter('val', $responsable)
            ->orderBy('e.evn_date_operation', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
