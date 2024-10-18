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



    //    /**
    //     * @return Evenement[] Returns an array of Evenement objects
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
}
