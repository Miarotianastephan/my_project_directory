<?php

namespace App\Repository;

use App\Entity\CompteMere;
use App\Entity\DetailBudget;
use App\Entity\Exercice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * @extends ServiceEntityRepository<DetailBudget>
 */
class DetailBudgetRepository extends ServiceEntityRepository
{
    private ExerciceRepository $exerciceRepository;
    private CompteMereRepository $compteMereRepository;
    private BudgetTypeRepository $budgetTypeRepository;

    public function __construct(ManagerRegistry      $registry,
                                ExerciceRepository   $exerciceRepo,
                                CompteMereRepository $compteMereRepo,
                                BudgetTypeRepository $budgetTypeRepo,
    )
    {
        $this->budgetTypeRepository = $budgetTypeRepo;
        $this->compteMereRepository = $compteMereRepo;
        $this->exerciceRepository = $exerciceRepo;
        parent::__construct($registry, DetailBudget::class);
    }

    /**
     * Récupère la liste des budgets selon l'exercice.
     *
     * Cette méthode permet de récupérer tous les budgets associés à un exercice particulier.
     * L'exercice est passé en paramètre et la méthode filtre les budgets pour ne retourner que ceux
     * qui sont liés à cet exercice précis. Cela permet de récupérer les budgets pour un exercice fiscal
     * ou une période spécifique.
     *
     * @param Exercice $exercice L'exercice pour lequel on souhaite récupérer les budgets associés.
     * @return array Un tableau contenant tous les budgets associés à l'exercice donné.
     *               Si aucun budget n'est trouvé, un tableau vide est retourné.
     */
    public function findByExercice(Exercice $exercice): array
    {
        $data = $this->createQueryBuilder('d')
            ->andWhere('d.exercice = :ex')
            // Définition du paramètre 'ex' pour l'exercice
            ->setParameter('ex', $exercice)
            ->getQuery()
            ->getResult();
        return $data;
    }

    /**
     * Ajoute un détail de budget pour un exercice, un compte mère et un type de budget donné.
     *
     * Cette méthode permet d'ajouter un nouveau détail de budget. Si un budget existe déjà pour
     * l'exercice et le compte mère spécifiés, la méthode retourne une réponse indiquant que le
     * budget existe déjà, avec les détails associés. Si aucun budget n'existe, un nouveau budget
     * est créé avec les informations fournies.
     *
     * - **Exercice** : L'exercice auquel appartient le budget. L'exercice est recherché à partir de son identifiant.
     * - **Compte Mère** : Le compte mère auquel le budget est associé. Le compte mère est recherché à partir de son identifiant.
     * - **Montant** : Le montant du budget à ajouter.
     * - **Type de Budget** : Le type de budget, spécifié par son identifiant. Ce type de budget est recherché avant l'insertion.
     *
     * Si un budget existe déjà pour l'exercice et le compte mère, la méthode retourne une réponse JSON indiquant
     * que le budget existe déjà. Sinon, elle ajoute un nouveau détail de budget et retourne une réponse JSON de succès.
     *
     * @param int $exercice_id L'identifiant de l'exercice pour lequel le détail de budget est ajouté.
     * @param int $compteMere_id L'identifiant du compte mère pour lequel le détail de budget est ajouté.
     * @param float $montant Le montant du budget à ajouter.
     * @param int $budgetType_id L'identifiant du type de budget.
     * @return JsonResponse La réponse JSON contenant le statut de l'ajout, un message et éventuellement des détails sur le budget existant.
     */
    public function ajoutDetailBudget(int   $exercice_id,
                                      int   $compteMere_id,
                                      float $montant,
                                      int   $budgetType_id): JsonResponse

    {
        $entityManager = $this->getEntityManager();

        // Recherche de l'exercice à partir de son identifiant
        $exercice = $this->exerciceRepository->find($exercice_id);
        if (!$exercice) {
            return new JsonResponse(['success' => false, 'message' => "L'exercice est introuvable", 'isExiste' => false]);
        }

        // Recherche du compte mère à partir de son identifiant
        $compteMere = $this->compteMereRepository->find($compteMere_id);
        if (!$compteMere) {
            return new JsonResponse(['success' => false, 'message' => "Le compte mere est introuvable", 'isExiste' => false]);
        }

        // Recherche du type de budget à partir de son identifiant
        $budgetType = $this->budgetTypeRepository->find($budgetType_id);
        if (!$budgetType) {
            return new JsonResponse(['success' => false, 'message' => "Le type de budget est introuvable", 'isExiste' => false]);
        }

        /**
         * Si le budget existe alors redirection vers une page de MAJ de budget
         *  * Sinon ajout de budget
         */
        $budget = $this->findByExerciceEtCpt($exercice, $compteMere);
        if ($budget) {
            return new JsonResponse(
                [
                    'success' => true,
                    'isExiste' => true,
                    'message' => "Le budget existe déja",
                    'exercice' => [
                        'id' => $budget->getExercice()->getId(),
                        'DateDebut' => $budget->getExercice()->getExerciceDateDebut()
                    ],
                    'cpt' => [
                        'id' => $budget->getCompteMere()->getId(),
                        'CptNumero' => $budget->getCompteMere()->getCptNumero(),
                        'CptLibelle' => $budget->getCompteMere()->getCptLibelle()
                    ],
                    'oldmontant' => $budget->getBudgetMontant(),
                    'newmontant' => $montant,
                    'detailbudget' => $budget->getId()
                ]);
        }

        // Si le budget n'existe pas, création d'un nouveau détail de budget
        $detail = new DetailBudget();
        $detail->setExercice($exercice);
        $detail->setCompteMere($compteMere);
        $detail->setBudgetMontant($montant);
        $detail->setBudgetDate(new \DateTime());
        $detail->setBudgetType($budgetType);
        try {
            $entityManager->persist($detail);
            $entityManager->flush();
            return new JsonResponse(['success' => true, 'message' => "Insertion réussi", 'isExiste' => false]);
        } catch (\Exception $exception) {
            return new JsonResponse(['success' => false, 'message' => $exception->getMessage()]);
        }

    }

    /**
     * Récupère un détail de budget pour un exercice et un compte mère donnés.
     *
     * La méthode vérifie d'abord si le numéro du compte mère appartient à une liste définie (statique) de comptes
     * associés à un certain type de budget. Si le numéro du compte est trouvé dans cette liste, elle récupère le
     * détail de budget directement pour l'exercice et le compte mère spécifiés.
     * Si ce n'est pas le cas, elle vérifie si le numéro du compte mère commence par un des préfixes associés à
     * des comptes spécifiques, et si c'est le cas, elle effectue la recherche avec un compte mère correspondant à ce préfixe.
     *
     * - **Exercice** : L'exercice auquel appartient le budget recherché.
     * - **Compte Mère** : Le compte mère auquel le budget est associé, utilisé pour rechercher le détail du budget.
     *
     * @param Exercice $exercice L'exercice pour lequel rechercher le détail de budget.
     * @param CompteMere $compteMere Le compte mère pour lequel rechercher le détail de budget.
     * @return DetailBudget|null Le détail de budget correspondant ou `null` si aucun détail de budget n'est trouvé.
     */
    public function findByExerciceEtCpt(Exercice $exercice, CompteMere $compteMere): ?DetailBudget
    {
        $data = null;
        // Accès à l'attribut static $listCompteDep
        $listCompteDep = CompteMereRepository::$listCompteDep;

        // Accès à l'attribut static $listCompteDepPrefixe
        $listCompteDepPrefixe = CompteMereRepository::$listCompteDepPrefixe;

        // Récupération du numéro de compte mère
        $cpt_numero = $compteMere->getCptNumero();

        // Vérifier si cpt_numero est présent dans listCompteDep
        if (in_array($cpt_numero, $listCompteDep)) {
            $data = $this->createQueryBuilder('d')
                ->andWhere('d.exercice = :ex')
                ->andWhere('d.compte_mere = :cpt')
                ->setParameter('ex', $exercice)
                ->setParameter('cpt', $compteMere)
                ->getQuery()
                ->getOneOrNullResult();
            //return $data;
        } else {
            // Vérifier si cpt_numero commence par un des préfixes dans listCompteDepPrefixe
            foreach ($listCompteDep as $prefixe) {
                // Vérification si le numéro du compte commence par un des préfixes
                if (str_starts_with($cpt_numero, $prefixe)) {
                    // Recherche du compte mère avec le préfixe correspondant
                    $compteMere = $this->compteMereRepository->findByCptNumero($prefixe);
                    if ($compteMere) {
                        $data = $this->createQueryBuilder('d')
                            ->andWhere('d.exercice = :ex')
                            ->andWhere('d.compte_mere = :cpt')
                            ->setParameter('ex', $exercice)
                            ->setParameter('cpt', $compteMere)
                            ->getQuery()
                            ->getOneOrNullResult();
                        return $data;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * Modifie le montant d'un détail de budget existant.
     *
     * Cette méthode permet de modifier le montant d'un détail de budget spécifique en fournissant l'ID du détail de budget
     * et le nouveau montant. Après la modification, elle persiste les changements dans la base de données.
     *
     * - **Détail de budget** : L'entité `DetailBudget` à mettre à jour, identifié par son ID.
     * - **Montant** : Le nouveau montant à attribuer au détail de budget.
     *
     * @param int $detail_budget_id L'ID du détail de budget à modifier.
     * @param float $montant Le nouveau montant à attribuer au détail de budget.
     * @return JsonResponse Un objet `JsonResponse` contenant le statut de l'opération et un message.
     */
    public function modifierDetailBudget(int   $detail_budget_id,
                                         float $montant): JsonResponse
    {
        // Récupération du gestionnaire d'entités
        $entityManager = $this->getEntityManager();

        // Recherche du détail de budget avec l'ID spécifié
        $detail_budget = $entityManager->find(DetailBudget::class, $detail_budget_id);
        $detail_budget->setBudgetMontant($montant);

        // Persister et flush des modifications
        try {
            $entityManager->persist($detail_budget); // Persist les modifications dans le gestionnaire d'entités
            $entityManager->flush(); // Effectue la mise à jour dans la base de données

            // Retour d'une réponse JSON avec un message de succès
            return new JsonResponse(['success' => true, 'message' => "Modification réussi"]);
        } catch (\Exception $exception) {
            // En cas d'erreur, retourne une réponse JSON avec un message d'erreur
            return new JsonResponse(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    /**
     * Récupère la somme du budget pour un exercice et un compte donnés.
     *
     * Cette méthode exécute une VIEW SQL pour récupérer la somme totale du budget associée à un exercice
     * spécifique et un compte donné à partir d'une vue dans la base de données.
     *
     * - **Exercice** : L'entité `Exercice` qui représente l'exercice pour lequel la somme est recherchée.
     * - **Compte** : Un identifiant de compte (sous forme de chaîne de caractères), généralement un préfixe de compte exemple "6".
     *
     * @param Exercice $exercice L'exercice pour lequel la somme doit être récupérée.
     * @param string $compte le préfixe de compte pour lequel la somme est calculée.
     * @return float|null La somme du budget pour le compte et l'exercice spécifiés, ou `null` si aucun résultat n'est trouvé ou en cas d'erreur.
     */
    function findSommeParExerciceEtCompte(Exercice $exercice, string $compte): ?float
    {

        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        // Requête SQL ajustée pour obtenir la somme du budget par exercice et compte
        $script = "SELECT total_budget
                FROM ce_v_somme_budget_compte 
                WHERE exercice_id = :exercice_id 
                AND premier_chiffre = :plan_compte";
        try {
            $statement = $connection->prepare($script);
            $statement->bindValue('exercice_id', $exercice->getId()); // Bind de l'ID de l'exercice
            $statement->bindValue('plan_compte', $compte); // Bind du compte ou préfixe de compte
            $resultSet = $statement->executeQuery();

            // Récupération du résultat sous forme associative
            $result = $resultSet->fetchAllAssociative();

            // Si un résultat est trouvé, retourne la somme
            if ($result) {
                return (float)$result[0]['TOTAL_BUDGET']; // Conversion en float et retour
            }
        } catch (\Exception $e) {
            // En cas d'erreur, afficher le message d'exception
            dump($e->getMessage());
        }

        // Si aucun résultat ou erreur, retourne null
        return null;
    }


    /**
     * Récupère la somme des budgets pour un exercice donné, et organise les résultats par compte.
     *
     * Cette méthode exécute un VIEW SQL pour récupérer les sommes de budget pour un exercice spécifique,
     * puis les organise par compte et catégorie. Elle utilise une vue dans la base de données (`ce_v_somme_budget_compte`).
     *
     * - **Exercice** : L'entité `Exercice` qui représente l'exercice pour lequel les sommes sont recherchées.
     *
     * @param Exercice $exercice L'exercice pour lequel les sommes des budgets doivent être récupérées.
     * @return array|null Un tableau associatif contenant les sommes par compte et catégorie, ou `null` si aucune donnée n'est trouvée ou en cas d'erreur.
     */
    function findSommeParCompte(Exercice $exercice)
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        // Requête SQL ajustée pour récupérer la somme des budgets, l'ID de l'exercice et le premier chiffre (compte)
        $script = "SELECT total_budget, exercice_id, premier_chiffre 
               FROM ce_v_somme_budget_compte 
               WHERE exercice_id = :exercice_id"; // Ajustement du nom de la colonne

        try {
            $statement = $connection->prepare($script);
            $statement->bindValue('exercice_id', $exercice->getId());
            $resultSet = $statement->executeQuery();

            // FetchAll pour récupérer plusieurs lignes
            // Récupération de toutes les lignes sous forme associative
            $results = $resultSet->fetchAllAssociative();
            $sommeParCompte = [];

            // Si des résultats sont trouvés, les traiter
            if ($results) {
                foreach ($results as $result) {
                    $sommeParCompte[] = [
                        'total_budget' => (float)$result['TOTAL_BUDGET'],  // Conversion en float
                        'exercice_id' => $result['EXERCICE_ID'],           // ID de l'exercice
                        'categorie' => $this->determinerCategorie($result['PREMIER_CHIFFRE']),  // Catégorie (basée sur le premier chiffre du compte)
                    ];
                }
                // Retourner le tableau contenant les sommes et catégories
                return $sommeParCompte;
            }
        } catch (\Exception $e) {
            // En cas d'erreur, afficher le message d'exception
            dump($e->getMessage());
        }
        // Si aucune donnée n'est trouvée ou en cas d'erreur, retourner null
        return null;
    }

    /**
     * Détermine la catégorie d'un compte financier en fonction de son numéro de compte.
     *
     * Cette méthode extrait le premier chiffre du numéro de compte, puis utilise ce chiffre pour déterminer la
     * catégorie du compte selon une classification prédéfinie.
     *
     * - **Numéro de compte** : Le numéro de compte est une chaîne de caractères où le premier caractère
     *   représente la classe du compte (par exemple, "1" pour les comptes de capitaux).
     *
     * @param string $numero Le numéro du compte sous forme de chaîne de caractères.
     * @return string La catégorie du compte en fonction de son premier chiffre.
     */
    function determinerCategorie(string $numero): string
    {
        // Extrait le premier chiffre du numéro de compte
        $premierChiffre = substr($numero, 0, 1);
        switch ($premierChiffre) {
            case '1':
                return 'Comptes de capitaux';
            case '2':
                return 'Comptes d\'immobilisations';
            case '3':
                return 'Comptes de stocks';
            case '4':
                return 'Comptes de tiers';
            case '5':
                return 'Comptes financiers';
            case '6':
                return 'Charges';
            case '7':
                return 'Produits';
            case '8':
                return 'Comptes spéciaux';
            default:
                return 'Autres';
        }
    }
}
