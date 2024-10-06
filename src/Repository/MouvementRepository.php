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
        $data = $this->createQueryBuilder('m')->join('m.mvt_evenement_id', 'ev')->where('ev.evn_exercice = :exercice')->setParameter('exercice', $exercice)->orderBy('ev.evn_date_operation', 'ASC')->getQuery()->getResult();
        dump($data);
        return $data;
    }

    //mode paiement = 1 => chèque
    //mode paiement = 2 => éspèce
    public function soldeDebitParModePaiement(Exercice $exercice, string $mode_paiement): ?float
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $table = $mode_paiement == 1 ? "v_mouvement_debit_siege" : "v_mouvement_debit_banque";

        $script = "SELECT COALESCE(SUM(param.mvt_montant), 0) AS total, ev.evn_exercice_id FROM $table param LEFT JOIN evenement ev ON param.mvt_evenement_id = ev.evn_id WHERE ev.evn_exercice_id = :exercice_id GROUP BY ev.evn_exercice_id";

        try {

            $statement = $connection->prepare($script);
            $statement->bindValue('exercice_id', $exercice->getId());
            $resultSet = $statement->executeQuery();
            $result = $resultSet->fetchAssociative();
            if ($result && array_key_exists('TOTAL', $result)) {
                return (float)$result['TOTAL'];
            }
            return null;
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
        return null;
    }

    public function soldeCreditParModePaiement(Exercice $exercice, string $mode_paiement) :?float
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $table = $mode_paiement == 1 ? "v_mouvement_credit_siege" : "v_mouvement_credit_banque";

        $script = "SELECT COALESCE(SUM(param.mvt_montant), 0) AS total, ev.evn_exercice_id FROM $table param LEFT JOIN evenement ev ON param.mvt_evenement_id = ev.evn_id WHERE ev.evn_exercice_id = :exercice_id GROUP BY ev.evn_exercice_id";

        try {

            $statement = $connection->prepare($script);
            $statement->bindValue('exercice_id', $exercice->getId());
            $resultSet = $statement->executeQuery();
            $result = $resultSet->fetchAssociative();
            if ($result && array_key_exists('TOTAL', $result)) {
                return (float)$result['TOTAL'];
            }
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
        return null;
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
        return $this->createQueryBuilder('m')->join('m.mvt_evenement_id', 'e')->orderBy('e.evn_date_operation', 'ASC')->addOrderBy('e.id', 'ASC')->getQuery()->getResult();
    }

    public function findAllMouvementById(): array
    {
        return $this->createQueryBuilder('m')->orderBy('m.id', 'ASC')->getQuery()->getResult();
    }

    public function getTotalMouvementGroupedByCompteMere(): array
    {
        return $this->createQueryBuilder('m')->select('cm.cpt_numero, SUM(m.mvt_montant) as total_montant')->join('m.mvt_compte_id', 'pc') // Jointure avec PlanCompte
        ->join('pc.compte_mere', 'cm') // Jointure avec CompteMere
        ->groupBy('cm.cpt_numero') // Groupement par le numéro de CompteMere
        ->getQuery()->getResult();
    }

    public function getTotalMouvementGroupedByPlanCompte(): array
    {
        return $this->createQueryBuilder('m')->select('pc.cpt_numero, SUM(m.mvt_montant) as total_montant')->join('m.mvt_compte_id', 'pc') // Jointure avec PlanCompte
        ->groupBy('pc.cpt_numero') // Groupement par le numéro de CompteMere
        ->getQuery()->getResult();
    }


}
