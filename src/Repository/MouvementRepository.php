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

    /*public function v_debit_banque_mensuel(Exercice $exercice): ?float
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        // Conversion de la date en chaîne (format 'Y-m-d')
        $date = $exercice->getExerciceDateDebut()->format('Y-m-d');
        dump($date);

        // Script SQL
        $script = "
        WITH calendrier AS (
            SELECT ADD_MONTHS(DATE '$date', LEVEL - 1) AS mois
            FROM DUAL
            CONNECT BY LEVEL <= 12
        ), exercices AS (
            SELECT DISTINCT evn_exercice_id
            FROM v_debit_banque_mensuel
        )
        SELECT
            e.evn_exercice_id,
            TO_CHAR(c.mois, 'YYYY-MM') AS mois_operation,
            COALESCE(v.total, 0) AS total
        FROM calendrier c
        CROSS JOIN exercices e
        LEFT JOIN v_debit_banque_mensuel v
            ON TO_CHAR(c.mois, 'YYYY-MM') = v.mois_operation
            AND e.evn_exercice_id = v.evn_exercice_id
        ORDER BY e.evn_exercice_id, c.mois
    ";

        try {
            // Préparation et exécution de la requête
            $statement = $connection->prepare($script);
            $resultSet = $statement->executeQuery();

            // Récupération de tous les résultats
            $results = $resultSet->fetchAllAssociative();

            // Vérification et traitement des résultats
            if (!empty($results)) {
                $total = 0.0;
                foreach ($results as $result) {
                    if (array_key_exists('total', $result)) {
                        $total += (float) $result['total'];
                    }
                }
                return $total;
            }

        } catch (\Exception $e) {
            // Gestion des erreurs et affichage du message d'erreur
            dump($e->getMessage());
        }

        // Retourne null en cas d'échec
        return null;
    }*/

    public function v_debit_banque_mensuel(Exercice $exercice): ?array
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        // Conversion de la date en chaîne (format 'Y-m-d')
        //$date = $exercice->getExerciceDateDebut()->format('Y-m-d');
        //dump($date);

        // Script SQL
        $script = "select total,mois_operation,EVN_EXERCICE_ID from v_debit_banque_mensuel where evn_exercice_id = :exercice";

        try {
            // Préparation et exécution de la requête
            $statement = $connection->prepare($script);
            $statement->bindValue('exercice', $exercice->getId());
            $resultSet = $statement->executeQuery();

            // Récupération de tous les résultats
            $results = $resultSet->fetchAllAssociative();

            // Si on obtient des résultats
            if (!empty($results)) {
                // Initialisation du tableau des mois avec 0 comme valeur par défaut
                $moisData = [
                    '01' => 0.0,
                    '02' => 0.0,
                    '03' => 0.0,
                    '04' => 0.0,
                    '05' => 0.0,
                    '06' => 0.0,
                    '07' => 0.0,
                    '08' => 0.0,
                    '09' => 0.0,
                    '10' => 0.0,
                    '11' => 0.0,
                    '12' => 0.0,
                ];

                // Parcourir les résultats pour affecter les valeurs aux mois correspondants
                foreach ($results as $result) {
                    $mois = substr($result['MOIS_OPERATION'], 5, 2); // Récupère le mois (par exemple "09")
                    $total = (float) $result['TOTAL']; // Convertit le total en float
                    $moisData[$mois] = $total; // Remplace la valeur 0 par la vraie valeur si trouvée
                }

                return $moisData; // Retourne le tableau des totaux par mois
            }

        } catch (\Exception $e) {
            // Gestion des erreurs et affichage du message d'erreur
            dump($e->getMessage());
        }

        // Retourne null en cas d'échec
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
