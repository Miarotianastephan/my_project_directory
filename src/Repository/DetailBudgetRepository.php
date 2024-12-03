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
     * Liste des budgets selon l'exercice
     * @param Exercice $exercice
     * @return array
     */
    public function findByExercice(Exercice $exercice): array
    {
        $data = $this->createQueryBuilder('d')
            ->andWhere('d.exercice = :ex')
            ->setParameter('ex', $exercice)
            ->getQuery()
            ->getResult();
        return $data;
    }

    /**
     * @param int $exercice_id
     * @param int $compteMere_id
     * @param float $montant
     * @param int $budgetType_id
     * @return JsonResponse
     */
    public function ajoutDetailBudget(int                    $exercice_id,
                                      int                    $compteMere_id,
                                      float                  $montant,
                                      int                    $budgetType_id): JsonResponse

    {
        $entityManager = $this->getEntityManager();
        $exercice = $this->exerciceRepository->find($exercice_id);
        if (!$exercice) {
            return new JsonResponse(['success' => false, 'message' => "L'exercice est introuvable", 'isExiste' => false]);
        }
        $compteMere = $this->compteMereRepository->find($compteMere_id);
        if (!$compteMere) {
            return new JsonResponse(['success' => false, 'message' => "Le compte mere est introuvable", 'isExiste' => false]);
        }
        $budgetType = $this->budgetTypeRepository->find($budgetType_id);
        if (!$budgetType) {
            return new JsonResponse(['success' => false, 'message' => "Le type de budget est introuvable", 'isExiste' => false]);
        }
        /*$budgetType = $this->budgetTypeRepository->find($budgetType_id);
        if (!$budgetType) {
            return new JsonResponse(['success' => false, 'message' => "Le budget type mere est introuvable"]);
        }*/

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
     * Liste des budgets selon l'exercice
     * @param Exercice $exercice
     * @param CompteMere $compteMere
     * @return DetailBudget|null
     */
    public function findByExerciceEtCpt(Exercice $exercice, CompteMere $compteMere): ?DetailBudget
    {
        $data = null;
        // Accès à l'attribut static $listCompteDep
        $listCompteDep = CompteMereRepository::$listCompteDep;
        // Accès à l'attribut static $listCompteDepPrefixe
        $listCompteDepPrefixe = CompteMereRepository::$listCompteDepPrefixe;
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
                // Utiliser str_starts_with (PHP 8) ou strpos
                if (str_starts_with($cpt_numero, $prefixe)) {
                    $compteMere = $this->compteMereRepository->findByCptNumero($prefixe);
                    if ($compteMere) {
                        //dump('Correspond à un préfixe dans listCompteDepPrefixe'.$compteMere->getCptNumero());
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

    public function modifierDetailBudget(int   $detail_budget_id,
                                         float $montant): JsonResponse
    {
        $entityManager = $this->getEntityManager();
        $detail_budget = $entityManager->find(DetailBudget::class, $detail_budget_id);
        //$budget = $detailBudgetRepository->findByExerciceEtCpt($detail_budget->getExercice(), $detail_budget->getCompteMere());
        $detail_budget->setBudgetMontant($montant);
        try {
            $entityManager->persist($detail_budget);
            $entityManager->flush();
            return new JsonResponse(['success' => true, 'message' => "Modification réussi"]);
        } catch (\Exception $exception) {
            return new JsonResponse(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    function findSommeParExerciceEtCompte(Exercice $exercice, string $compte): ?float
    {

        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();
        // Requête ajustée avec les colonnes correctes
        $script = "SELECT total_budget
                FROM ce_v_somme_budget_compte 
                WHERE exercice_id = :exercice_id 
                AND premier_chiffre = :plan_compte";
        try {
            $statement = $connection->prepare($script);
            $statement->bindValue('exercice_id', $exercice->getId());
            $statement->bindValue('plan_compte', $compte);
            $resultSet = $statement->executeQuery();
            $result = $resultSet->fetchAllAssociative();
            if ($result) {
                return (float)$result[0]['TOTAL_BUDGET'];
            }
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
        return null;
    }

    function findSommeParCompte(Exercice $exercice)
    {
        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        // Requête ajustée avec les colonnes correctes
        $script = "SELECT total_budget, exercice_id, premier_chiffre 
               FROM ce_v_somme_budget_compte 
               WHERE exercice_id = :exercice_id"; // Ajustement du nom de la colonne

        try {
            $statement = $connection->prepare($script);
            $statement->bindValue('exercice_id', $exercice->getId());
            $resultSet = $statement->executeQuery();

            // FetchAll pour récupérer plusieurs lignes
            $results = $resultSet->fetchAllAssociative();
            $sommeParCompte = [];

            // Si des résultats sont trouvés, les traiter
            if ($results) {
                foreach ($results as $result) {
                    $sommeParCompte[] = [
                        'total_budget' => (float)$result['TOTAL_BUDGET'],  // Nom correct
                        'exercice_id' => $result['EXERCICE_ID'],          // Nom correct
                        'categorie' => $this->determinerCategorie($result['PREMIER_CHIFFRE']),         // Nom correct
                    ];
                }
                return $sommeParCompte;
            }
        } catch (\Exception $e) {
            dump($e->getMessage());
        }
        return null;
    }


    function determinerCategorie(string $numero): string
    {
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

    //    /**
    //     * @return DetailBudget[] Returns an array of DetailBudget objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?DetailBudget
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
