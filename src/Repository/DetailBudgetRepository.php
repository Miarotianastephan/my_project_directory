<?php

namespace App\Repository;

use App\Entity\BudgetType;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailBudget::class);
    }

    public function findByExerciceEtCpt(Exercice $exercice, CompteMere $compteMere): ?DetailBudget
    {
        $data = $this->createQueryBuilder('d')
            ->andWhere('d.exercice = :ex')
            ->andWhere('d.compte_mere = :cpt')
            ->setParameter('ex', $exercice)
            ->setParameter('cpt', $compteMere)
            ->getQuery()
            ->getOneOrNullResult();
        return $data;
    }

    public function ajoutDetailBudget(int                    $exercice_id,
                                      int                    $compteMere_id,
                                      float                  $montant,
                                      int                    $budgetType_id,
                                      DetailBudgetRepository $detailBudgetRepository): JsonResponse

    {
        $entityManager = $this->getEntityManager();
        $exercice = $entityManager->find(Exercice::class, $exercice_id);
        if (!$exercice) {
            return new JsonResponse(['success' => false, 'message' => "L\'exercice est introuvable"]);
        }
        $compteMere = $entityManager->find(CompteMere::class, $compteMere_id);
        if (!$compteMere) {
            return new JsonResponse(['success' => false, 'message' => "Le compte mere est introuvable"]);
        }
        $budgetType = $entityManager->find(BudgetType::class, $budgetType_id);
        if (!$budgetType) {
            return new JsonResponse(['success' => false, 'message' => "Le budget type mere est introuvable"]);
        }

        $budget = $detailBudgetRepository->findByExerciceEtCpt($exercice, $compteMere);
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
            return new JsonResponse(
                ['success' => true, 'message' => "Insertion réussi", 'isExiste' => false]);
        } catch (\Exception $exception) {
            return new JsonResponse(['success' => false, 'message' => $exception->getMessage()]);
        }

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
            return new JsonResponse(
                ['success' => true, 'message' => "Modification réussi"]);
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
