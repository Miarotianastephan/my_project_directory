<?php

namespace App\Repository;

use App\Entity\CompteMere;
use App\Entity\Exercice;
use App\Entity\Mouvement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mouvement>
 */
class MouvementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mouvement::class);
    }

    public function findByExercice(Exercice $exercice): ?array
    {
        $data = $this->createQueryBuilder('m')
            ->join('m.mvt_evenement_id', 'ev')
            ->where('ev.evn_exercice = :exercice')
            ->setParameter('exercice', $exercice)
            ->orderBy('ev.evn_date_operation', 'ASC')
            ->getQuery()
            ->getResult();
        dump($data);
        return $data;
    }

    public function soldeDebitByExerciceByCompteMere(Exercice $exercice, CompteMere $compte): float
    {
        $result = $this->createQueryBuilder('e')
            ->select('SUM(e.mvt_montant)')
            // Jointure avec PlanCompte (via mvt_compte_id)
            ->join('e.mvt_compte_id', 'pc')
            // Jointure avec CompteMere (via plan_compte_id.compte_mere)
            ->join('pc.compte_mere', 'cm')
            // Condition sur l'exercice
            ->join('e.mvt_evenement_id', 'ev')
            ->where('ev.evn_exercice = :exercice')
            // Condition sur le compte mère
            ->andWhere('cm = :compte')
            // Condition sur le mouvement débit
            ->andWhere('e.isMvtDebit = true')
            ->setParameter('exercice', $exercice)
            ->setParameter('compte', $compte)
            ->getQuery()
            ->getSingleScalarResult();

        // Si le résultat est null, on retourne 0.0
        return $result !== null ? (float)$result : 0.0;
        //return $result;
    }


    public function soldeCreditByExerciceByCompteMere(Exercice $exercice, CompteMere $compte): float
    {
        $result = $this->createQueryBuilder('e')
            ->select('SUM(e.mvt_montant)')
            // Jointure avec PlanCompte (via mvt_compte_id)
            ->join('e.mvt_compte_id', 'pc')
            // Jointure avec CompteMere (via plan_compte_id.compte_mere)
            ->join('pc.compte_mere', 'cm')
            // Condition sur l'exercice
            ->join('e.mvt_evenement_id', 'ev')
            ->where('ev.evn_exercice = :exercice')
            // Condition sur le compte mère
            ->andWhere('cm = :compte')
            // Condition sur le mouvement débit
            ->andWhere('e.isMvtDebit = false')
            ->setParameter('exercice', $exercice)
            ->setParameter('compte', $compte)
            ->getQuery()
            ->getSingleScalarResult();

        // Si le résultat est null, on retourne 0.0
        return $result !== null ? (float)$result : 0.0;
        //return $result;
    }

    //    /**
    //     * @return Mouvement[] Returns an array of Mouvement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Mouvement
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
