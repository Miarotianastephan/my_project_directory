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
            // ->andWhere('cm = :compte')
            // Condition sur le mouvement débit
            ->andWhere('e.isMvtDebit = true')
            ->setParameter('exercice', $exercice)
            // ->setParameter('compte', $compte)
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

    /**
     * @return Mouvement[] Returns an array of Mouvement objects
     */
    public function findAllOrderedByEventDateAndId(): array
    {
        return $this->createQueryBuilder('m')
            ->join('m.mvt_evenement_id', 'e')
            ->orderBy('e.evn_date_operation', 'ASC')
            ->addOrderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllMouvementById(): array
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getTotalMouvementGroupedByCompteMere(): array
    {
        return $this->createQueryBuilder('m')
            ->select('cm.cpt_numero, SUM(m.mvt_montant) as total_montant')
            ->join('m.mvt_compte_id', 'pc') // Jointure avec PlanCompte
            ->join('pc.compte_mere', 'cm') // Jointure avec CompteMere
            ->groupBy('cm.cpt_numero') // Groupement par le numéro de CompteMere
            ->getQuery()
            ->getResult();
    }

    public function getTotalMouvementGroupedByPlanCompte(): array
    {
        return $this->createQueryBuilder('m')
            ->select('pc.cpt_numero, SUM(m.mvt_montant) as total_montant')
            ->join('m.mvt_compte_id', 'pc') // Jointure avec PlanCompte
            ->groupBy('pc.cpt_numero') // Groupement par le numéro de CompteMere
            ->getQuery()
            ->getResult();
    }
    

}
