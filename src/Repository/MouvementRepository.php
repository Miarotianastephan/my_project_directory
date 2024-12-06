<?php

namespace App\Repository;

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

    /**
     * Récupère la liste des mouvements associés à un exercice donné.
     *
     * Cette méthode permet de récupérer tous les mouvements (ou logs) liés à un
     * exercice spécifique en fonction de son identifiant. Elle effectue une jointure
     * avec l'entité `mvt_evenement_id` pour lier les événements à l'exercice et
     * renvoie les résultats triés par date d'opération dans l'ordre croissant.
     *
     * @param Exercice $exercice L'exercice pour lequel on souhaite récupérer
     *                           les mouvements associés.
     *
     * @return array|null Un tableau des mouvements associés à l'exercice, ou
     *                    `null` si aucun résultat n'est trouvé. Chaque élément
     *                    du tableau représente un mouvement lié à l'exercice.
     */
    public function findByExercice(Exercice $exercice): ?array
    {
        $data = $this->createQueryBuilder('m')->join('m.mvt_evenement_id', 'ev')->where('ev.evn_exercice = :exercice')->setParameter('exercice', $exercice)->orderBy('ev.evn_date_operation', 'ASC')->getQuery()->getResult();
        return $data;
    }

    /**
     * Récupère le solde total des débits pour un exercice en fonction du mode de paiement donnés.
     *
     * Cette méthode permet de calculer le total des débits pour un exercice spécifique,
     * en fonction du mode de paiement choisi (chèque ou espèces). Elle utilise un VIEW SQL
     * pour sélectionner la VIEW appropriée (`ce_v_mouvement_debit_siege` pour espèces,
     * et `ce_v_mouvement_debit_banque` pour chèque) et effectue un calcul de la somme des montants
     * des mouvements correspondants. Le résultat est renvoyé en tant que nombre à virgule flottante
     * représentant le total des débits.
     *
     * @param Exercice $exercice L'exercice pour lequel le solde des débits est calculé.
     * @param string $mode_paiement Le mode de paiement pour lequel le calcul est effectué.
     *   * **`0` pour espèces**
     *   * **`1` pour chèque**
     *
     * @return float|null Le total des débits correspondant au mode de paiement et à l'exercice,
     *                    ou `null` si aucun résultat n'est trouvé ou en cas d'erreur.
     */
    public function soldeDebitParModePaiement(Exercice $exercice, string $mode_paiement): ?float
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        // Choix de la table en fonction du mode de paiement
        $table = $mode_paiement == 0 ? "ce_v_mouvement_debit_siege" : "ce_v_mouvement_debit_banque";

        $script = "SELECT COALESCE(SUM(param.mvt_montant), 0) AS total, ev.evn_exercice_id FROM $table param LEFT JOIN ce_evenement ev ON param.mvt_evenement_id = ev.evn_id WHERE ev.evn_exercice_id = :exercice_id GROUP BY ev.evn_exercice_id";

        try {

            $statement = $connection->prepare($script);
            $statement->bindValue('exercice_id', $exercice->getId());
            $resultSet = $statement->executeQuery();
            $result = $resultSet->fetchAssociative();

            // Vérification du résultat et retour du total des débits
            if ($result && array_key_exists('TOTAL', $result)) {
                return (float)$result['TOTAL'];
            }
            return null;
        } catch (\Exception $e) {
            // En cas d'erreur, affichage du message d'exception (peut être retiré en production)
            dump($e->getMessage());
        }
        return null;
    }

    /**
     * Récupère le solde total des crédits pour un exercice et un mode de paiement donnés.
     *
     * Cette méthode permet de calculer le total des crédits pour un exercice spécifique,
     * en fonction du mode de paiement choisi (chèque ou espèces). Elle utilise une requête SQL
     * dynamique pour sélectionner la table appropriée (`ce_v_mouvement_credit_siege` pour espèces,
     * et `ce_v_mouvement_credit_banque` pour chèque) et effectue un calcul de la somme des montants
     * des mouvements correspondants. Le résultat est renvoyé sous forme de nombre à virgule flottante
     * représentant le total des crédits.
     *
     * @param Exercice $exercice L'exercice pour lequel le solde des crédits est calculé.
     * @param string $mode_paiement Le mode de paiement pour lequel le calcul est effectué.
     *                             - `0` pour espèces
     *                             - `1` pour chèque
     *
     * @return float|null Le total des crédits correspondant au mode de paiement et à l'exercice,
     *                    ou `null` si aucun résultat n'est trouvé ou en cas d'erreur.
     */
    public function soldeCreditParModePaiement(Exercice $exercice, string $mode_paiement): ?float
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        // Choix de la table en fonction du mode de paiement
        $table = $mode_paiement == 0 ? "ce_v_mouvement_credit_siege" : "ce_v_mouvement_credit_banque";

        $script = "SELECT COALESCE(SUM(param.mvt_montant), 0) AS total, ev.evn_exercice_id FROM $table param LEFT JOIN ce_evenement ev ON param.mvt_evenement_id = ev.evn_id WHERE ev.evn_exercice_id = :exercice_id GROUP BY ev.evn_exercice_id";

        try {

            $statement = $connection->prepare($script);
            $statement->bindValue('exercice_id', $exercice->getId());
            $resultSet = $statement->executeQuery();
            $result = $resultSet->fetchAssociative();

            // Vérification du résultat et retour du total des crédits
            if ($result && array_key_exists('TOTAL', $result)) {
                return (float)$result['TOTAL'];
            }
        } catch (\Exception $e) {
            // En cas d'erreur, affichage du message d'exception (peut être retiré en production)
            dump($e->getMessage());
        }
        return null;
    }

    /**
     * Récupère les débits de caisse mensuels pour un exercice donné.
     *
     * Cette méthode permet de récupérer les totaux des débits de caisse pour chaque mois
     * d'un exercice spécifique. Elle exécute une VIEW SQL qui sélectionne les totaux des débits
     * pour chaque mois de l'exercice et renvoie un tableau contenant les montants correspondants,
     * avec chaque index représentant un mois (de 1 à 12). Si un mois n'a pas de débit enregistré,
     * la valeur correspondante reste à 0.
     *
     * @param Exercice $exercice L'exercice pour lequel les débits mensuels sont récupérés.
     *
     * @return array|null Un tableau de 12 éléments représentant les totaux des débits de caisse
     *                    pour chaque mois (indexé de 1 à 12). Si aucune donnée n'est trouvée,
     *                    retourne `null`.
     */
    public function v_debit_caisse_mensuel(Exercice $exercice): ?array
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        // Script SQL pour récupérer les débits mensuels de caisse
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
     * Récupère les totaux des débits bancaires mensuels pour un exercice donné.
     *
     * Cette méthode interroge la base de données pour récupérer les informations de débit bancaire
     * mensuelles associées à un exercice spécifique. Elle retourne un tableau contenant les totaux
     * des débits pour chaque mois de l'année, où chaque indice du tableau représente un mois
     * (de janvier à décembre). Si aucune donnée n'est trouvée, la méthode retourne `null`.
     *
     * @param Exercice $exercice L'exercice pour lequel les données doivent être récupérées.
     * @return array|null Un tableau des totaux mensuels des débits bancaires, ou `null` si aucune donnée n'est trouvée.
     */
    public function v_debit_banque_mensuel(Exercice $exercice): ?array
    {
        // Obtention de l'entity manager et de la connexion à la base de données
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        // Script SQL pour récupérer les totaux et mois associés à un exercice
        $script = "select total,mois_operation,EVN_EXERCICE_ID from ce_v_debit_banque_mensuel where evn_exercice_id = :exercice";

        try {
            // Préparation et exécution de la requête
            $statement = $connection->prepare($script);
            $statement->bindValue('exercice', $exercice->getId());
            $resultSet = $statement->executeQuery();

            // Récupération des résultats sous forme de tableau associatif
            $results = $resultSet->fetchAllAssociative();

            // Si on obtient des résultats
            if (!empty($results)) {
                // Initialisation du tableau des mois (12 mois, initialisés à 0)
                $moisData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

                // Parcourir les résultats pour affecter les valeurs aux mois correspondants
                foreach ($results as $result) {
                    $mois = substr($result['MOIS_OPERATION'], 5, 2); // Récupère le mois (par exemple "09")
                    $total = (float)$result['TOTAL']; // Convertit le total en float

                    // Mise à jour du tableau des mois avec les totaux correspondants
                    $moisData[(int)$mois] = $total; // Remplace la valeur 0 par la vraie valeur si trouvée
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

    /**
     * Récupère les totaux des débits bancaires mensuels pour un exercice donné.
     *
     * Cette méthode interroge la base de données pour récupérer les informations de débit bancaire
     * mensuelles associées à un exercice spécifique. Elle retourne un tableau contenant les totaux
     * des débits pour chaque mois de l'année, où chaque indice du tableau représente un mois
     * (de janvier à décembre). Si aucune donnée n'est trouvée, la méthode retourne `null`.
     *
     * @param Exercice $exercice L'exercice pour lequel les données doivent être récupérées.
     * @return array|null Un tableau des totaux mensuels des débits bancaires, ou `null` si aucune donnée n'est trouvée.
     */
    public function v_debit_banque_annuel(Exercice $exercice): ?float
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        // Script SQL pour récupérer les totaux et mois associés à un exercice
        $script = "select SUM(total) as total from ce_v_debit_banque_mensuel where evn_exercice_id = :exercice";
        try {
            // Préparation et exécution de la requête
            $statement = $connection->prepare($script);
            $statement->bindValue('exercice', $exercice->getId()); // Liaison de l'ID de l'exercice
            $resultSet = $statement->executeQuery();

            // Récupération des résultats sous forme de tableau associatif
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
     * Récupère tous les mouvements, triés par date d'opération de l'événement et par ID.
     *
     * Cette méthode permet de récupérer tous les objets `Mouvement` (représentant les mouvements financiers ou autres)
     * associés à un événement, triés d'abord par la date d'opération (`evn_date_operation`) de l'événement,
     * puis par l'ID de l'événement en ordre croissant.
     *
     * La méthode utilise un `QueryBuilder` pour construire une requête SQL avec une jointure entre la table des
     * mouvements (`m`) et la table des événements (`e`), puis exécute la requête et retourne le résultat sous forme d'un tableau
     * d'objets `Mouvement`.
     *
     * @return Mouvement[] Un tableau d'objets `Mouvement` triés par date d'opération de l'événement et par ID de l'événement.
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

    /**
     * Récupère le solde restant par mouvement (somme des débits et crédits) par numéro de compte.
     *
     * Cette méthode exécute une requête qui permet de calculer le solde restant par compte en fonction des mouvements associés.
     * Elle effectue un calcul des totaux des débits et des crédits pour chaque compte, puis calcule le solde restant en
     * soustrayant la somme des crédits de la somme des débits. Les résultats sont groupés par numéro de compte.
     *
     * Les champs retournés par cette requête sont :
     * - `cpt_numero` : le numéro du compte (plan comptable),
     * - `total_debit` : la somme totale des montants des mouvements de débit,
     * - `total_credit` : la somme totale des montants des mouvements de crédit,
     * - `total_montant` : le solde restant, calculé comme la différence entre le total des débits et des crédits.
     *
     * @return array Un tableau contenant les résultats, chaque entrée représentant un compte avec ses totaux de débit, crédit et le solde restant.
     */
    public function getSoldeRestantByMouvement(): array
    {
        return $this->createQueryBuilder('m')
            ->select('pc.cpt_numero, SUM(CASE WHEN m.isMvtDebit = 1 THEN m.mvt_montant ELSE 0 END) as total_debit, SUM(CASE WHEN m.isMvtDebit = 0 THEN m.mvt_montant ELSE 0 END) as total_credit, (SUM(CASE WHEN m.isMvtDebit = 1 THEN m.mvt_montant ELSE 0 END) - SUM(CASE WHEN m.isMvtDebit = 0 THEN m.mvt_montant ELSE 0 END)) as total_montant')
            ->join('m.mvt_compte_id', 'pc') // Jointure avec la table PlanCompte ('pc') en utilisant 'mvt_compte_id' de la table 'm'
            ->groupBy('pc.cpt_numero') // Groupement par le numéro de CompteMere
            ->getQuery()->getResult();
    }

    /**
     * Recherche des mouvements en fonction de critères spécifiques (numéro de compte, libellé, date).
     *
     * Cette méthode permet de rechercher des mouvements en fonction de différents critères de filtrage, tels que le numéro de compte,
     * le libellé du compte, et une plage de dates. Si aucun critère n'est fourni, elle retourne tous les mouvements.
     *
     * - Si le numéro de compte est spécifié, la recherche est effectuée sur les comptes dont le numéro commence par la valeur donnée.
     * - Si le libellé du compte est spécifié, la recherche est effectuée sur les comptes dont le libellé contient la valeur donnée.
     * - Si des dates sont spécifiées, la recherche est limitée aux mouvements dont la date d'opération de l'événement se trouve
     *   dans l'intervalle des dates données (inclusive).
     *
     * Les résultats sont triés par la date d'opération de l'événement (`evn_date_operation`), de manière croissante.
     *
     * @param string|null $rech_numero Le numéro de compte à rechercher (peut être partiel, commence par la valeur donnée).
     * @param string|null $rech_libelle Le libellé du compte à rechercher (peut être partiel, contient la valeur donnée).
     * @param string|null $date_inf La date inférieure (au format 'Y-m-d') pour filtrer les résultats, ou `null` si non spécifiée.
     * @param string|null $date_sup La date supérieure (au format 'Y-m-d') pour filtrer les résultats, ou `null` si non spécifiée.
     * @return array Un tableau de résultats sous forme de tableau associatif avec les mouvements trouvés, chaque entrée contenant :
     *               - `mvn_id` : L'ID du mouvement.
     *               - `mvt_evenement_id` : L'ID de l'événement lié au mouvement.
     *               - `mvt_compte_id` : L'ID du compte associé au mouvement.
     *               - `mvt_montant` : Le montant du mouvement.
     *               - `is_mvt_debit` : Indicateur de débit (1 pour débit, 0 pour crédit).
     *               - `evn_date_operation` : La date d'opération de l'événement au format 'DD/MM/YY'.
     *               - `cpt_numero` : Le numéro du compte.
     *               - `cpt_libelle` : Le libellé du compte.
     */
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
        $sql .= " ORDER BY ev.evn_date_operation ASC";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery($params);

        // Retourner les résultats
        return $resultSet->fetchAllAssociative();
    }

    /**
     * Récupère tous les mouvements associés à un événement spécifique.
     *
     * Cette méthode permet de récupérer tous les mouvements comptables associés à un événement donné.
     * Elle prend un objet `Evenement` en paramètre et effectue une requête pour obtenir tous les mouvements
     * (objets `Mouvement`) qui sont liés à cet événement, en se basant sur l'ID de l'événement.
     *
     * Les mouvements retournés correspondent à l'événement spécifié et sont récupérés à l'aide d'un `QueryBuilder`.
     *
     * @param Evenement $evn L'événement pour lequel on souhaite récupérer les mouvements associés.
     *
     * @return array|null Un tableau de résultats contenant les mouvements associés à l'événement spécifié,
     *                    ou `null` si aucun mouvement n'est trouvé.
     */
    public function findAllMvtByEvenement(Evenement $evn): ?array
    {
        return $this->createQueryBuilder('mvt')
            ->where('mvt.mvt_evenement_id = :evenement')
            ->setParameter('evenement', $evn)
            ->getQuery()
            ->getResult();
    }

    /**
     * Effectue la comptabilisation directe d'une transaction.
     *
     * Cette méthode permet de comptabiliser une transaction en créant un événement comptable
     * ainsi que les mouvements de débit et de crédit correspondants. Elle vérifie d'abord que les informations
     * nécessaires sont valides (code de transaction, responsable, exercice, comptes de débit et crédit),
     * puis crée les objets nécessaires pour enregistrer l'événement et les mouvements dans la base de données.
     * Si une erreur se produit à n'importe quelle étape, une réponse d'erreur est retournée.
     *
     * - L'événement est créé avec les informations fournies, y compris le montant et la référence générée automatiquement.
     * - Un mouvement de débit est créé pour le compte de débit spécifié et un mouvement de crédit est créé pour
     *   le compte de crédit spécifié.
     * - Si l'opération réussit, un message de succès est renvoyé.
     * - Si une erreur survient, la transaction est annulée et un message d'erreur est renvoyé.
     *
     * @param string $date La date de la comptabilisation, au format 'YYYY-MM-DD'.
     * @param string $entite L'entité concernée par la transaction.
     * @param string $transaction Le code de la transaction à comptabiliser.
     * @param string $compte_debit_numero Le numéro de compte de débit à utiliser pour le mouvement comptable.
     * @param string $compte_credit_numero Le numéro de compte de crédit à utiliser pour le mouvement comptable.
     * @param string $montant Le montant de la transaction.
     * @param int $user_responsable L'ID de l'utilisateur responsable de la comptabilisation.
     *
     * @return JsonResponse Retourne une réponse JSON avec le statut de la comptabilisation.
     *                      Si l'opération réussit, retourne un message de succès, sinon un message d'erreur.
     */
    public function comptabilisation_directe(string $date, string $entite,
                                             string $transaction, string $compte_debit_numero,
                                             string $compte_credit_numero, string $montant, int $user_responsable): JsonResponse
    {
        // Recherche des données nécessaires à la comptabilisation
        $transaction_a_faire = $this->transactionTypeRepository->findTransactionByCode($transaction);
        $responsable = $this->utilisateurRepository->find($user_responsable);
        $exercice = $this->exerciceRepository->getExerciceValide();
        $compte_debit = $this->planCompteRepository->findByNumero($compte_debit_numero);
        $compte_credit = $this->planCompteRepository->findByNumero($compte_credit_numero);

        // Vérification de la validité des données
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

        // Démarrage de la transaction dans la base de données
        $entityManager = $this->getEntityManager();
        $entityManager->beginTransaction();
        try {
            //création de l'evenement
            $evenement = new Evenement($transaction_a_faire, $responsable, $exercice, $entite, (float)$montant, "default", new DateTime());
            $entityManager->persist($evenement);

            // Génération de la référence pour l'événement
            $ref_evn = "DIR/" . date('Y') . "/" . $evenement->getId();
            $evenement->setEvnReference($ref_evn);

            //CREATION DE MOUVEMENT
            $mv_debit = new Mouvement($evenement, $compte_debit, (float)$montant, true);                        // DEBIT
            $entityManager->persist($mv_debit);

            $mv_credit = new Mouvement($evenement, $compte_credit, (float)$montant, false);                        // DEBIT
            $entityManager->persist($mv_credit);
            $entityManager->flush();
            $entityManager->commit();
        } catch (\Exception $e) {
            dump($e->getMessage());
            // En cas d'erreur, annulation de la transaction
            $entityManager->rollback();
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
        return new JsonResponse(['success' => true, 'message' => 'comptabilisation réussi']);
    }

}
