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

        $table = $mode_paiement == 0 ? "ce_v_mouvement_debit_siege" : "ce_v_mouvement_debit_banque";
        /*dump([
            "TEZSTSET" => $table,
        ]);*/

        $script = "SELECT COALESCE(SUM(param.mvt_montant), 0) AS total, ev.evn_exercice_id FROM $table param LEFT JOIN ce_evenement ev ON param.mvt_evenement_id = ev.evn_id WHERE ev.evn_exercice_id = :exercice_id GROUP BY ev.evn_exercice_id";

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
        // 0 = paiement espèces 
        $table = $mode_paiement == 0 ? "ce_v_mouvement_credit_siege" : "ce_v_mouvement_credit_banque";

        $script = "SELECT COALESCE(SUM(param.mvt_montant), 0) AS total, ev.evn_exercice_id FROM $table param LEFT JOIN ce_evenement ev ON param.mvt_evenement_id = ev.evn_id WHERE ev.evn_exercice_id = :exercice_id GROUP BY ev.evn_exercice_id";

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



    public function v_debit_caisse_mensuel(Exercice $exercice): ?array
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        // Script SQL
        $script = "select total,mois_operation,EVN_EXERCICE_ID from ce_v_debit_caisse_mensuel where evn_exercice_id = :exercice";
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
                $moisData = [ 0 , 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

                // Parcourir les résultats pour affecter les valeurs aux mois correspondants
                foreach ($results as $result) {
                    $mois = substr($result['MOIS_OPERATION'], 5, 2); // Récupère le mois (par exemple "09")
                    $total = (float) $result['TOTAL']; // Convertit le total en float
                    //dump("Mois=".$mois );

                    $moisData[(int)$mois] = $total; // Remplace la valeur 0 par la vraie valeur si trouvée
                }
                dump($moisData);
                return $moisData; // Retourne le tableau des totaux par mois
            }

        } catch (\Exception $e) {
            // Gestion des erreurs et affichage du message d'erreur
            dump($e->getMessage());
        }

        // Retourne null en cas d'échec
        return null;

    }
    public function v_debit_banque_mensuel(Exercice $exercice): ?array
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        // Conversion de la date en chaîne (format 'Y-m-d')
        //$date = $exercice->getExerciceDateDebut()->format('Y-m-d');
        //dump($date);

        // Script SQL
        $script = "select total,mois_operation,EVN_EXERCICE_ID from ce_v_debit_banque_mensuel where evn_exercice_id = :exercice";

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
                $moisData = [ 0 , 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

                // Parcourir les résultats pour affecter les valeurs aux mois correspondants
                foreach ($results as $result) {
                    $mois = substr($result['MOIS_OPERATION'], 5, 2); // Récupère le mois (par exemple "09")
                    $total = (float) $result['TOTAL']; // Convertit le total en float
                    //dump("Mois=".$mois );

                    $moisData[(int)$mois] = $total; // Remplace la valeur 0 par la vraie valeur si trouvée
                }
                dump($moisData);
                return $moisData; // Retourne le tableau des totaux par mois
            }

        } catch (\Exception $e) {
            // Gestion des erreurs et affichage du message d'erreur
            dump($e->getMessage());
        }

        // Retourne null en cas d'échec
        return null;
    }


    /**
     * @return Mouvement[] Returns an array of Mouvement objects
     */
    public function findAllOrderedByEventDateAndId(): array
    {
        return $this->createQueryBuilder('m')->join('m.mvt_evenement_id', 'e')->orderBy('e.evn_date_operation', 'ASC')->addOrderBy('e.id', 'ASC')->getQuery()->getResult();
    }

    public function findAllMouvementById(): array
    {      
        $conn = $this->getEntityManager()->getConnection();

        // Construction de la requête SQL
        $sql = "SELECT 
                m.mvn_id,m.mvt_evenement_id,m.mvt_compte_id,m.mvt_montant,m.is_mvt_debit,
                ev.evn_date_operation,pc.cpt_numero,pc.cpt_libelle 
                FROM ce_mouvement m
                JOIN ce_plan_compte pc ON m.mvt_compte_id = pc.cpt_id
                JOIN ce_evenement ev ON m.mvt_evenement_id = ev.evn_id
                WHERE 1=1 ORDER BY m.mvn_id ASC";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        return $resultSet->fetchAllAssociative();
    }

    public function getTotalMouvementGroupedByCompteMere(): array
    {
        return $this->createQueryBuilder('m')->select('cm.cpt_numero, SUM(m.mvt_montant) as total_montant')
        ->join('m.mvt_compte_id', 'pc') // Jointure avec PlanCompte
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

    // public function searchDataMouvement($rech_numero=null, $rech_libelle=null, $date_inf=null, $date_sup=null):array{
    //     if(is_null($rech_numero) && is_null($rech_libelle) && is_null($date_inf) && is_null($date_sup))
    //     {
    //         return $this->findAllMouvementById();
    //     } 
    //     $queryBuilder = $this->createQueryBuilder('m');
    //     $queryBuilder->join('m.mvt_compte_id', 'pc');           // Jointure avec PlanCompte
    //     $queryBuilder->join('m.mvt_evenement_id', 'ev');           // Jointure avec PlanCompte
    //     if(!is_null($rech_numero)){
    //         $queryBuilder->where('pc.cpt_numero LIKE :numero')
    //         ->setParameter('numero', $rech_numero.'%');         // begin with %xxx%
    //     }
    //     if(!is_null($rech_libelle)){
    //         $queryBuilder->andWhere('pc.cpt_libelle LIKE :libelle')
    //         ->setParameter('libelle', '%'.$rech_libelle.'%');   // contient %xxx%
    //     }
    //     // Si les deux dates sont fournies
    //     // Conversion des chaînes en objets DateTime
    //     $dateInf = $date_inf ? \DateTime::createFromFormat('Y-m-d', $date_inf) : null;
    //     $dateSup = $date_sup ? \DateTime::createFromFormat('Y-m-d', $date_sup) : null;
    //     if (!is_null($date_inf) && !is_null($date_sup)) {
    //         $queryBuilder->where('TRUNC(ev.evn_date_operation) BETWEEN TRUNC(:dateInf) AND TRUNC(:dateSup)')
    //         ->setParameter('dateInf', $dateInf)
    //         ->setParameter('dateSup', $dateSup);
    //     } elseif (!is_null($date_inf)) {
    //         // Si seulement la date inférieure est fournie
    //         $queryBuilder->where('TRUNC(ev.evn_date_operation) >= TRUNC(:dateInf)')
    //         ->setParameter('dateInf', $dateInf);
    //     } elseif (!is_null($date_sup)) {
    //         // Si seulement la date supérieure est fournie
    //         $queryBuilder->where('TRUNC(ev.evn_date_operation) <= TRUNC(:dateSup)')
    //         ->setParameter('dateSup', $dateSup);
    //     }
    //     return $queryBuilder->getQuery()->getResult();
    // }
    public function searchDataMouvement($rech_numero = null, $rech_libelle = null, $date_inf = null, $date_sup = null): array
    {
        // Si tous les paramètres sont null, on retourne tous les mouvements
        if (is_null($rech_numero) && is_null($rech_libelle) && is_null($date_inf) && is_null($date_sup)) {
            return $this->findAllMouvementById();
        }

        // Connexion à la base de données
        $conn = $this->getEntityManager()->getConnection();

        // Construction de la requête SQL
        $sql = "SELECT 
                m.mvn_id,m.mvt_evenement_id,m.mvt_compte_id,m.mvt_montant,m.is_mvt_debit,
                TO_CHAR(ev.evn_date_operation, 'DD/MM/YY') AS evn_date_operation,pc.cpt_numero,pc.cpt_libelle 
                FROM ce_mouvement m
                JOIN ce_plan_compte pc ON m.mvt_compte_id = pc.cpt_id
                JOIN ce_evenement ev ON m.mvt_evenement_id = ev.evn_id
                WHERE 1=1";

        // Paramètres pour la requête
        $params = [];

        // Ajout de la condition pour le numéro de compte
        if (!is_null($rech_numero)) {
            $sql .= " AND pc.cpt_numero LIKE :numero";
            $params['numero'] = $rech_numero . '%'; // Recherche commence par
        }

        // Ajout de la condition pour le libellé du compte
        if (!is_null($rech_libelle)) {
            $sql .= " AND pc.cpt_libelle LIKE :libelle";
            $params['libelle'] = '%' . $rech_libelle . '%'; // Recherche contenant
        }

        // Conversion des dates inférieure et supérieure
        $dateInf = $date_inf ? \DateTime::createFromFormat('Y-m-d', $date_inf) : null;
        $dateSup = $date_sup ? \DateTime::createFromFormat('Y-m-d', $date_sup) : null;

        // Ajout de la condition pour l'intervalle de dates
        if (!is_null($dateInf) && !is_null($dateSup)) {
            $sql .= " AND TRUNC(ev.evn_date_operation) BETWEEN TRUNC(TO_DATE(:dateInf, 'YYYY-MM-DD')) AND TRUNC(TO_DATE(:dateSup, 'YYYY-MM-DD'))";
            $params['dateInf'] = $dateInf->format('Y-m-d');
            $params['dateSup'] = $dateSup->format('Y-m-d');
        } elseif (!is_null($dateInf)) {
            $sql .= " AND TRUNC(ev.evn_date_operation) >= TRUNC(TO_DATE(:dateInf, 'YYYY-MM-DD'))";
            $params['dateInf'] = $dateInf->format('Y-m-d');
        } elseif (!is_null($dateSup)) {
            $sql .= " AND TRUNC(ev.evn_date_operation) <= TRUNC(TO_DATE(:dateSup, 'YYYY-MM-DD'))";
            $params['dateSup'] = $dateSup->format('Y-m-d');
        }

        // Préparation et exécution de la requête
        dump($sql);
        $sql .= " ORDER BY ev.evn_date_operation ASC";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery($params);

        // Retourner les résultats
        return $resultSet->fetchAllAssociative();
    }




}
