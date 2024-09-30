<?php

namespace App\Controller;

use App\Repository\BudgetTypeRepository;
use App\Repository\CompteMereRepository;
use App\Repository\DetailBudgetRepository;
use App\Repository\EvenementRepository;
use App\Repository\ExerciceRepository;
use App\Repository\MouvementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/budget')]
class BudgetController extends AbstractController
{
    private $user;

    public function __construct(Security $security)
    {
        // $this->security = $security;
        $this->user = $security->getUser();
    }

    #[Route('/', name: 'tresorier.liste_budget', methods: ['GET'])]
    public function liste_budget(DetailBudgetRepository $detailBudgetRepository,
                                 ExerciceRepository     $exerciceRepository): Response
    {
        $exercice = $exerciceRepository->find(61);
        if (!$exercice) {
            return new Response("Budget introuvable", 404);
        }
        $liste_budget = $detailBudgetRepository->findByExercice($exercice);
        return $this->render('budget/liste_budget.html.twig',
            [
                'liste_budget' => $liste_budget
            ]
        );
    }

    #[Route('/ajout_budget', name: 'tresorier.form_budget', methods: ['GET'])]
    public function form_budget(ExerciceRepository   $exerciceRepository,
                                CompteMereRepository $compteMereRepository): Response
    {
        $date = new \DateTime();
        return $this->render('tresorier/ajout_budget.html.twig',
            [
                'exercices' => $exerciceRepository->getExerciceValide($date),
                'plan_comptes' => $compteMereRepository->findAll()
            ]
        );
    }

    #[Route('/ajout/budget', name: 'tresorier.ajout_budget', methods: ['POST'])]
    public function ajout_budget(Request                $request,
                                 DetailBudgetRepository $detailBudgetRepository,
                                 BudgetTypeRepository   $budgetTypeRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $montant = $data['montant'] ?? null;
        $exercice_id = $data['exercice'] ?? null;
        $plan_cpt = $data['plan_cpt'] ?? null;
        $budgetType_id = $budgetTypeRepository->findOneByLibelle("Investissement");

        //$budgetType_id = 1;
        if (!$montant) {
            return new JsonResponse(['success' => false, 'message' => "Le montant est obligatoire"]);
        }
        if (!$exercice_id) {
            return new JsonResponse(['success' => false, 'message' => "L'exercice est obligatoire"]);
        }
        if (!$plan_cpt) {
            return new JsonResponse(['success' => false, 'message' => "Le plan de compte est obligatoire"]);
        }
        if (!$budgetType_id) {
            return new JsonResponse(['success' => false, 'message' => "Problème dans budget type controller budgetType_id = Investissement introuvable"]);
        }

        $addbase = $detailBudgetRepository->ajoutDetailBudget($exercice_id, $plan_cpt, $montant, $budgetType_id->getId(), $detailBudgetRepository);
        $addbase = json_decode($addbase->getContent(), true);


        if ($addbase['isExiste']) {
            return new JsonResponse(
                [
                    'success' => $addbase['success'],
                    'isExiste' => $addbase['isExiste'],
                    'message' => $addbase['message'],
                    'exercice' => $addbase['exercice'],
                    'cpt' => $addbase['cpt'],
                    'oldmontant' => $addbase['oldmontant'],
                    'newmontant' => $addbase['newmontant'],
                    'detailbudget' => $addbase['detailbudget']
                ]);
        } else {
            return new JsonResponse(
                [
                    'success' => $addbase['success'],
                    'message' => $addbase['message'],
                    'url' => $this->generateUrl("tresorier.liste_budget")
                ]);
        }
    }

    #[Route('/suivi_budgetaire', name: 'tresorier.suivi_budgetaire', methods: ['GET'])]
    public function suivi_budgetaire(Request              $request,
                                     CompteMereRepository $compteMereRepository,
                                     ExerciceRepository   $exerciceRepository,
                                     MouvementRepository $mouvementRepository): JsonResponse
    {
        $exerice = $exerciceRepository->find(41);
        $cpt_mere = $compteMereRepository->find(42);
        $solde_debit = $mouvementRepository->soldeDebitByExerciceByCompteMere($exerice,$cpt_mere) ;
        $solde_CREDIT = $mouvementRepository->soldeCreditByExerciceByCompteMere($exerice,$cpt_mere) ;

        return new JsonResponse(
            [
                'success' => true,
                'solde_debit' => $solde_debit,
                'solde_credit' => $solde_CREDIT,
            ]
        );
    }

    #[Route('/modifier/budget', name: 'tresorier.modifier_budget', methods: ['POST'])]
    public function modifier_budget(Request                $request,
                                    DetailBudgetRepository $detailBudgetRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $montant = $data['montant'] ?? null;
        $detail_budget = $data['detail_budget'] ?? null;

        //$budgetType = $budgetTypeRepository->find(2);
        if (!$montant) {
            return new JsonResponse(['success' => false, 'message' => "Le montant est obligatoire"]);
        }
        if (!$detail_budget) {
            return new JsonResponse(['success' => false, 'message' => "Le detail_budget est obligatoire"]);
        }

        $addbase = $detailBudgetRepository->modifierDetailBudget($detail_budget, $montant);
        $addbase = json_decode($addbase->getContent(), true);

        return new JsonResponse(
            [
                'success' => $addbase['success'],
                'message' => $addbase['message'],
                'url' => $this->generateUrl("tresorier.liste_budget")
            ]);
    }
}