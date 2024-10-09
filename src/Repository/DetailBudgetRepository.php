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

    public function findByExercice(Exercice $exercice): array
    {
        $data = $this->createQueryBuilder('d')
            ->andWhere('d.exercice = :ex')
            ->setParameter('ex', $exercice)
            ->getQuery()
            ->getResult();
        return $data;
    }

    public function ajoutDetailBudget(int                    $exercice_id,
                                      int                    $compteMere_id,
                                      float                  $montant,
                                      int                    $budgetType_id,
                                      DetailBudgetRepository $detailBudgetRepository): JsonResponse

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
