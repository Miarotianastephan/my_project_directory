<?php

namespace App\Controller;

use App\Entity\DemandeType;
use App\Repository\BanqueRepository;
use App\Repository\BudgetTypeRepository;
use App\Repository\ChequierRepository;
use App\Repository\CompteMereRepository;
use App\Repository\DemandeTypeRepository;
use App\Repository\DetailBudgetRepository;
use App\Repository\DetailDemandePieceRepository;
use App\Repository\ExerciceRepository;
use App\Repository\LogDemandeTypeRepository;
use App\Repository\PlanCompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Date;

#[Route('/tresorier')]
class TresorierController extends AbstractController
{
    private $user;

    public function __construct(Security $security)
    {
        // $this->security = $security;
        $this->user = $security->getUser();
    }

    #[Route('/', name: 'tresorier.liste_demande_en_attente', methods: ['GET'])]
    public function index(DemandeTypeRepository $dm_Repository): Response
    {
        return $this->render('tresorier/index.html.twig', [
            'demande_types' => $dm_Repository->findByEtat(200)
        ]);

    }

    #[Route('/demande/{id}', name: 'tresorier.detail_demande_en_attente', methods: ['GET'])]
    public function show($id, EntityManagerInterface $entityManager, DetailDemandePieceRepository $demandePieceRepository): Response
    {
        $data = $entityManager->find(DemandeType::class, $id);
        $list_img = $demandePieceRepository->findByDemandeType($data);
        return $this->render('tresorier/show.html.twig', ['demande_type' => $data, 'images' => $list_img]);
    }

    #[Route('/demande/valider/{id}', name: 'tresorier.valider_fond', methods: ['GET'])]
    public function valider_fond($id, DemandeTypeRepository $dm_type): Response
    {
        $data = $dm_type->find($id);
        $montant = 20000;
        return $this->render('tresorier/deblocker_fond.html.twig',
            ['demande_type' => $data, 'montant' => $montant]
        );
    }

    #[Route('/remettre_fond/{id}', name: 'tresorier.remettre_fond', methods: ['POST'])]
    public function remettre_fond($id, LogDemandeTypeRepository $logDemandeTypeRepository): JsonResponse
    {
        $id_user_tresorier = $this->user->getId();
        $rep = $logDemandeTypeRepository->ajoutDeblockageFond($id, $id_user_tresorier); // Déblocage du fonds demandée
        // Comptabilisation de l'opération de décaissement
            // à compléter
        // fin comptabilisation
        
        $data = json_decode($rep->getContent(), true);
        if ($data['success'] == true) {
            return new JsonResponse([
                'success' => true,
                'message' => 'La demande a été remis',
                'path' => $this->generateUrl('tresorier.liste_demande_en_attente')
            ]);
        } else {
            return new JsonResponse([
                'success' => false,
                'message' => $data['message']
            ]);
        }
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
        $exercice = $data['exercice'] ?? null;
        $plan_cpt = $data['plan_cpt'] ?? null;
        $budgetType = 2;
        //$budgetType = $budgetTypeRepository->find(2);
        if (!$montant) {
            return new JsonResponse(['success' => false, 'message' => "Le montant est obligatoire"]);
        }
        if (!$exercice) {
            return new JsonResponse(['success' => false, 'message' => "L\'exercice est obligatoire"]);
        }
        if (!$plan_cpt) {
            return new JsonResponse(['success' => false, 'message' => "Le plan de compte est obligatoire"]);
        }

        $addbase = $detailBudgetRepository->ajoutDetailBudget($exercice, $plan_cpt, $montant, $budgetType, $detailBudgetRepository);
        $addbase = json_decode($addbase->getContent(), true);

        if ($addbase['isExiste']) {
            return new JsonResponse(
                [
                    'success' => $addbase['success'],
                    'isExiste' => $addbase['success'],
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
                    'url' => "Voici un url"
                ]);
        }
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
                'url' => "Voici un url"
            ]);
    }

    #[Route('/demande_approvisionnement', name: 'tresorier.form_approvisionnement', methods: ['GET'])]
    public function form_approvisionnement(PlanCompteRepository $planCompteRepository,
                                           BanqueRepository     $banqueRepository): Response
    {

        $list_cpt_numero = ["510001", "510002", "510003", "510004", "510005", "510006", "510007", "510008", "510009", "510010", "510011", "611000"];
        $liste_entite = array_filter(array_map(
            fn($code) => $planCompteRepository->findByNumero($code),
            $list_cpt_numero
        ));


        $situation_caisse = 10000;
        $liste_banque = $banqueRepository->findAll();

        return $this->render('tresorier/demande_approvisionnement.html.twig', [
            'entites' => $liste_entite,
            'banques' => $liste_banque,
            'situation_caisse' => $situation_caisse,
        ]);
    }

    #[Route('/ajout_approvisionnement', name: 'tresorier.ajout_approvisionnement', methods: ['POST'])]
    public function ajout_approvisionnement(Request               $request,
                                            DemandeTypeRepository $demandeTypeRepository,
                                            ChequierRepository    $chequierRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $date = $data['date_dm'] ?? null;
        $caisse = ['caisse'] ?? null;
        $entite = $data['entite'] ?? null;
        $montant = $data['montant'] ?? null;
        $banque = $data['banque'] ?? null;
        $chequier = $data['chequier'] ?? null;

        // Validation des données
        if (!$date) {
            return new JsonResponse(['success' => false, 'message' => "La date est nécessaire"]);
        }
        if (!$caisse) {
            return new JsonResponse(['success' => false, 'message' => "La situation de caisse est nécessaire"]);
        }
        if (!$entite) {
            return new JsonResponse(['success' => false, 'message' => "Le choix d'entité est nécessaire"]);
        }
        if (!$montant) {
            return new JsonResponse(['success' => false, 'message' => "Le montant est nécessaire"]);
        }
        if (!$banque) {
            return new JsonResponse(['success' => false, 'message' => "Le choix de banque est nécessaire"]);
        }
        if (!$chequier) {
            return new JsonResponse(['success' => false, 'message' => "Le choix de chéquier est nécessaire"]);
        }

        $reponse = $demandeTypeRepository->ajout_approvisionnement($entite, $banque, $chequier, $montant, $chequierRepository);
        $reponse = json_decode($reponse->getContent(), true);

        return new JsonResponse(
            [
                'success' => $reponse['success'],
                'message' => $reponse['message'],
                'url' => "Voici un url"
            ]
        );
    }

}
