<?php

namespace App\Repository;

use App\Entity\CompteMere;
use App\Entity\Evenement;
use App\Entity\Exercice;
use App\Entity\Mouvement;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<Mouvement>
 */
class MouvementRepository extends ServiceEntityRepository
{
    private TransactionTypeRepository $transactionTypeRepository;
    private UtilisateurRepository $utilisateurRepository;
    private ExerciceRepository $exerciceRepository;
    private PlanCompteRepository $planCompteRepository;
    public function __construct(ManagerRegistry           $registry,
                                TransactionTypeRepository $trsTypeRepo,
                                UtilisateurRepository     $utilisateurRepo,
                                ExerciceRepository        $exerciceRepo,
                                PlanCompteRepository      $plnCompteRepo)
    {
        parent::__construct($registry, Mouvement::class);
        $this->transactionTypeRepository = $trsTypeRepo;
        $this->utilisateurRepository = $utilisateurRepo;
        $this->exerciceRepository = $exerciceRepo;
        $this->planCompteRepository = $plnCompteRepo;

    }


    public function findByExercice(Exercice $exercice): ?array
    {
        $data = $this->createQueryBuilder('m')->join('m.mvt_evenement_id', 'ev')->where('ev.evn_exercice = :exercice')->setParameter('exercice', $exercice)->orderBy('ev.evn_date_operation', 'ASC')->getQuery()->getResult();
        dump($data);
        return $data;
    }


    public function comptabilisation_directe(string $date, string $entite,
                                             string $transaction, string $compte_debit_numero,
                                             string $compte_credit_numero, string $montant, int $user_responsable): JsonResponse
    {

        $transaction_a_faire = $this->transactionTypeRepository->findTransactionByCode($transaction);
        $responsable = $this->utilisateurRepository->find($user_responsable);
        $exercice = $this->exerciceRepository->getExerciceValide();
        $compte_debit = $this->planCompteRepository->findByNumero($compte_debit_numero);
        $compte_credit = $this->planCompteRepository->findByNumero($compte_credit_numero);
        if (!$transaction_a_faire) {
            return new JsonResponse(['success' => false, 'message' => "Veuillez verifier le code de transaction"]);
        } elseif (!$responsable) {
            return new JsonResponse(['success' => false, 'message' => "Veuillez verifier le responsable"]);
        } elseif (!$exercice) {
            return new JsonResponse(['success' => false, 'message' => "Veuillez verifier l'exercice"]);
        } elseif (!$compte_debit) {
            return new JsonResponse(['success' => false, 'message' => "Veuillez verifier le code de debit"]);
        } elseif (!$compte_credit) {
            return new JsonResponse(['success' => false, 'message' => "Veuillez verifier le code de credit"]);
        }


        $entityManager = $this->getEntityManager();
        $entityManager->beginTransaction();
        try {
            //création de l'evenement
            $evenement = new Evenement();
            $evenement->setEvnTrsId($transaction_a_faire);
            $evenement->setEvnResponsable($responsable);
            $evenement->setEvnExercice($exercice);
            $evenement->setEvnCodeEntity($entite);
            $evenement->setEvnMontant((float)$montant);
            $evenement->setEvnReference("DIR/2024/01");
            $evenement->setEvnDateOperation(new DateTime());
            $entityManager->persist($evenement);


            //CREATION DE MOUVEMENT
            $mv_debit = new Mouvement();                        // DEBIT
            $mv_debit->setMvtEvenementId($evenement);
            $mv_debit->setMvtMontant((float)$montant);
            $mv_debit->setMvtDebit(true);
            $mv_debit->setMvtCompteId($compte_debit);
            $entityManager->persist($mv_debit);

            $mv_credit = new Mouvement();                        // DEBIT
            $mv_credit->setMvtEvenementId($evenement);
            $mv_credit->setMvtMontant((float)$montant);
            $mv_credit->setMvtDebit(false);
            $mv_credit->setMvtCompteId($compte_credit);
            $entityManager->persist($mv_credit);

            $entityManager->flush();
            $entityManager->commit();
        } catch (\Exception $e) {
            dump($e->getMessage());
            $entityManager->rollback();
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);

        }
        return new JsonResponse(['success' => true, 'message' => 'comptabilisation réussi']);
    }
    //mode paiement = 1 => chèque
    //mode paiement = 0 => éspèce
    public function soldeDebitParModePaiement(Exercice $exercice, string $mode_paiement): ?float
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $table = $mode_paiement == 0 ? "ce_v_mouvement_debit_siege" : "ce_v_mouvement_debit_banque";

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

    public function soldeCreditParModePaiement(Exercice $exercice, string $mode_paiement): ?float
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();
        // 0 = paiement espèces
        // 1 = chèque
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
                $moisData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

                // Parcourir les résultats pour affecter les valeurs aux mois correspondants
                foreach ($results as $result) {
                    $mois = substr($result['MOIS_OPERATION'], 5, 2); // Récupère le mois (par exemple "09")
                    $total = (float)$result['TOTAL']; // Convertit le total en float
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
                $moisData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

                // Parcourir les résultats pour affecter les valeurs aux mois correspondants
                foreach ($results as $result) {
                    $mois = substr($result['MOIS_OPERATION'], 5, 2); // Récupère le mois (par exemple "09")
                    $total = (float)$result['TOTAL']; // Convertit le total en float
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

    public function v_debit_banque_annuel(Exercice $exercice): ?float
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();
        $script = "select SUM(total) as total from ce_v_debit_banque_mensuel where evn_exercice_id = :exercice";
        try {
            // Préparation et exécution de la requête
            $statement = $connection->prepare($script);
            $statement->bindValue('exercice', $exercice->getId());
            $resultSet = $statement->executeQuery();
            $results = $resultSet->fetchAllAssociative();
            if (!empty($results)) {
                return (float)$results[0]['TOTAL'];
            }
        } catch (\Exception $e) {
            // Gestion des erreurs et affichage du message d'erreur
            dump($e->getMessage());
        }
        return null;
    }

    public function v_debit_caisse_annuel(Exercice $exercice): ?float
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();
        $script = "select SUM(total) as total from ce_v_debit_caisse_mensuel where evn_exercice_id = :exercice";
        try {
            // Préparation et exécution de la requête
            $statement = $connection->prepare($script);
            $statement->bindValue('exercice', $exercice->getId());
            $resultSet = $statement->executeQuery();
            $results = $resultSet->fetchAllAssociative();
            if (!empty($results)) {
                return (float)$results[0]['TOTAL'];
            }
        } catch (\Exception $e) {
            // Gestion des erreurs et affichage du message d'erreur
            dump($e->getMessage());
        }
        return null;
    }

    /**
     * @return Mouvement[] Returns an array of Mouvement objects
     */
    public function findAllOrderedByEventDateAndId(): array
    {
        return $this->createQueryBuilder('m')->join('m.mvt_evenement_id', 'e')->orderBy('e.evn_date_operation', 'ASC')->addOrderBy('e.id', 'ASC')->getQuery()->getResult();
    }

    public function getTotalMouvementGroupedByCompteMere(): array
    {
        return $this->createQueryBuilder('m')->select('cm.cpt_numero, SUM(m.mvt_montant) as total_montant')
            ->join('m.mvt_compte_id', 'pc') // Jointure avec PlanCompte
            ->join('pc.compte_mere', 'cm') // Jointure avec CompteMere
            ->groupBy('cm.cpt_numero') // Groupement par le numéro de CompteMere
            ->getQuery()->getResult();
    }

    // Fonction pour avoir la somme des montants
    public function getTotalMouvementGroupedByPlanCompte(): array
    {
        return $this->createQueryBuilder('m')
        ->select('pc.cpt_numero, SUM(m.mvt_montant) as total_montant')
        ->join('m.mvt_compte_id', 'pc') // Jointure avec PlanCompte
        ->groupBy('pc.cpt_numero') // Groupement par le numéro de CompteMere
        ->getQuery()->getResult();
    }
    // Ajustment de la fonction 
    public function getSoldeRestantByMouvement(): array
    {
        return $this->createQueryBuilder('m')
            ->select('pc.cpt_numero, SUM(CASE WHEN m.isMvtDebit = 1 THEN m.mvt_montant ELSE 0 END) as total_debit, SUM(CASE WHEN m.isMvtDebit = 0 THEN m.mvt_montant ELSE 0 END) as total_credit, (SUM(CASE WHEN m.isMvtDebit = 1 THEN m.mvt_montant ELSE 0 END) - SUM(CASE WHEN m.isMvtDebit = 0 THEN m.mvt_montant ELSE 0 END)) as total_montant')
            ->join('m.mvt_compte_id', 'pc') // Jointure avec PlanCompte
            // ->join('pc.compte_mere', 'cm') // Jointure avec CompteMere
            ->groupBy('pc.cpt_numero') // Groupement par le numéro de CompteMere
            ->getQuery()->getResult();
    }

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

    public function findAllMvtByEvenement(Evenement $evn): ?array{
        return $this->createQueryBuilder('mvt')
        ->where('mvt.mvt_evenement_id = :evenement')
        ->setParameter('evenement', $evn)
        ->getQuery()
        ->getResult();
    }

    public function comptabilisation_directe(string $date, string $entite,
        string $transaction, string $compte_debit_numero,
        string $compte_credit_numero, string $montant, int $user_responsable): JsonResponse
    {

        $transaction_a_faire = $this->transactionTypeRepository->findTransactionByCode($transaction);
        $responsable = $this->utilisateurRepository->find($user_responsable);
        $exercice = $this->exerciceRepository->getExerciceValide();
        $compte_debit = $this->planCompteRepository->findByNumero($compte_debit_numero);
        $compte_credit = $this->planCompteRepository->findByNumero($compte_credit_numero);
        if (!$transaction_a_faire) {
        return new JsonResponse(['success' => false, 'message' => "Veuillez verifier le code de transaction"]);
        } elseif (!$responsable) {
        return new JsonResponse(['success' => false, 'message' => "Veuillez verifier le responsable"]);
        } elseif (!$exercice) {
        return new JsonResponse(['success' => false, 'message' => "Veuillez verifier l'exercice"]);
        } elseif (!$compte_debit) {
        return new JsonResponse(['success' => false, 'message' => "Veuillez verifier le code de debit"]);
        } elseif (!$compte_credit) {
        return new JsonResponse(['success' => false, 'message' => "Veuillez verifier le code de credit"]);
        }


        $entityManager = $this->getEntityManager();
        $entityManager->beginTransaction();
        try {
        //création de l'evenement
        $evenement = new Evenement($transaction_a_faire,$responsable,$exercice,$entite,(float)$montant,"default",new DateTime());
        // $evenement->setEvnTrsId($transaction_a_faire);
        // $evenement->setEvnResponsable($responsable);
        // $evenement->setEvnExercice($exercice);
        // $evenement->setEvnCodeEntity($entite);
        // $evenement->setEvnMontant((float)$montant);
        // $evenement->setEvnReference("DIR/2024/01");
        // $evenement->setEvnDateOperation(new DateTime());
        $entityManager->persist($evenement);
        $ref_evn = "DIR/" . date('Y') . "/" . $evenement->getId();
        $evenement->setEvnReference($ref_evn);

        //CREATION DE MOUVEMENT
        $mv_debit = new Mouvement($evenement,$compte_debit,(float)$montant,true);                        // DEBIT
        // $mv_debit->setMvtEvenementId($evenement);
        // $mv_debit->setMvtMontant((float)$montant);
        // $mv_debit->setMvtDebit(true);
        // $mv_debit->setMvtCompteId($compte_debit);
        $entityManager->persist($mv_debit);

        $mv_credit = new Mouvement($evenement,$compte_credit,(float)$montant,false);                        // DEBIT
        // $mv_credit->setMvtEvenementId($evenement);
        // $mv_credit->setMvtMontant((float)$montant);
        // $mv_credit->setMvtDebit(false);
        // $mv_credit->setMvtCompteId($compte_credit);
        $entityManager->persist($mv_credit);
        dump("MAND");
        $entityManager->flush();
        $entityManager->commit();
        } catch (\Exception $e) {
            dump($e->getMessage());
            $entityManager->rollback();
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);

        }
        return new JsonResponse(['success' => true, 'message' => 'comptabilisation réussi']);
    }

}
