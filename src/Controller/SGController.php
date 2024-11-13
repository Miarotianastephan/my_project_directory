<?php

namespace App\Controller;

use App\Repository\CompteMereRepository;
use App\Repository\DemandeTypeRepository;
use App\Repository\DetailBudgetRepository;
use App\Repository\DetailDemandePieceRepository;
use App\Repository\LogDemandeTypeRepository;
use App\Repository\MouvementRepository;
use App\Repository\ObservationDemandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sg')]
class SGController extends AbstractController
{
    private $user;

    public function __construct(Security $security)
    {
        // $this->security = $security;
        $this->user = $security->getUser();
    }

    #[Route(path: '/', name: 'SG.liste_demande_en_attente', methods: ['GET'])]
    public function index(DemandeTypeRepository $dm_Repository): Response
    {

        $demandes_attente_validation = $dm_Repository->findDemandeAttentes();
        return $this->render('sg/index.html.twig', [
            'demande_types' => $demandes_attente_validation
        ]);
    }


    #[Route('/demande/modifier/{id}', name: 'SG.modifier_en_attente', methods: ['GET'])]
    public function modifier($id, DemandeTypeRepository $dm_Repository, DetailDemandePieceRepository $demandePieceRepository, MouvementRepository $mouvementRepository, DetailBudgetRepository $detailBudgetRepository, CompteMereRepository $compteMereRepository): Response
    {
        $data = $dm_Repository->find($id);
        $list_img = $demandePieceRepository->findByDemandeType($data);

        //debit = Mivoka
        //credit = Miditra

        $exercice = $data->getExercice();                   // Avoir l'exercice liée au demande
        $solde_debit = $mouvementRepository->soldeDebitParModePaiement($exercice, $data->getDmModePaiement());
        $solde_CREDIT = $mouvementRepository->soldeCreditParModePaiement($exercice, $data->getDmModePaiement());


        $compte_mere = $data->getPlanCompte()->getCompteMere();
        $budget = $detailBudgetRepository->findByExerciceEtCpt($exercice, $compte_mere);
        if ($budget != null) {
            $budget = $budget->getBudgetMontant();
        }
        if ($solde_debit == null) {
            $solde_reste = 0;
        } else if ($solde_CREDIT == null) {
            $solde_reste = $solde_debit;
        } else {
            $solde_reste = $solde_debit - $solde_CREDIT;
        }

        return $this->render('sg/modifier_demande.html.twig', [
            'demande_type' => $data,
            'images' => $list_img,
            'solde_reste' => $solde_reste,
            'budget_reste' => $budget - $solde_debit,
            'budget' => $budget
        ]);
    }

    #[Route(path: '/modifier/{id}', name: 'sg.modifier', methods: ['POST'])]
    public function modifier_post($id, Request $request, LogDemandeTypeRepository $logDemandeTypeRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $commentaire_data = $data['commentaire'] ?? null;
        $id_user_sg = $this->user->getId(); // mandeha io

        if (!$commentaire_data) {
            return new JsonResponse(['success' => false, 'message' => "Pas de modification reçu"]);
        }
        $rep = $logDemandeTypeRepository->ajoutModifierDemande($id, $id_user_sg, $commentaire_data);
        $data = json_decode($rep->getContent(), true);
        if ($data['success'] == true) {
            return new JsonResponse(['success' => true, 'message' => $data['message'], 'path' => $this->generateUrl('SG.liste_demande_en_attente')]);
        } else {
            return new JsonResponse(['success' => false, 'message' => $data['message']]);
        }
    }

    #[Route('/{id}', name: 'SG.detail_demande_en_attente', methods: ['GET'])]
    public function show($id,
                         MouvementRepository $mouvementRepository,
                         DemandeTypeRepository $dm_Repository,
                         DetailDemandePieceRepository $demandePieceRepository,
                         DetailBudgetRepository $detailBudgetRepository): Response
    {
        $data = $dm_Repository->find($id);
        $list_img = $demandePieceRepository->findByDemandeType($data);

        $exercice = $data->getExercice();                   // Avoir l'exercice liée au demande
        $solde_debit = $mouvementRepository->soldeDebitParModePaiement($exercice, $data->getDmModePaiement());
        $solde_CREDIT = $mouvementRepository->soldeCreditParModePaiement($exercice, $data->getDmModePaiement());
        $compte_mere = $data->getPlanCompte()->getCompteMere();
        $budget = $detailBudgetRepository->findByExerciceEtCpt($exercice, $compte_mere);
        if ($budget != null) {
            $budget = $budget->getBudgetMontant();
        }
        if ($solde_debit == null) {
            $solde_reste = 0;
        } else if ($solde_CREDIT == null) {
            $solde_reste = $solde_debit;
        } else {
            $solde_reste = $solde_debit - $solde_CREDIT;
        }

        $solde_reste = 70000;
        $budget = 200000 + 100000 + 100000 + 200000 + 90000 + 500000 + 150000;;
        $depense = 70000 + 100000 + 30000 + 10000 + 50000 + 90000 +75000 +  200000 + 100000 + 100000 + 200000 + 90000 + 500000 + 150000;
        $budget_reste = $budget - $depense;

        //dump($solde_debit ."debit".$solde_CREDIT."credit".$solde_reste."reste".$exercice);
        return $this->render('sg/show.html.twig', [
            'demande_type' => $data,
            'images' => $list_img,
            'solde_reste' => $solde_reste,
            'budget' => $budget ,
            'budget_reste' => $budget_reste,
            //ALANA ITO RANDY
            //'budget_reste' => $budget - $solde_debit,
        ]);
    }

    #[Route('/demande/valider/{id}', name: 'SG.valider_en_attente', methods: ['GET'])]
    public function valider($id, MouvementRepository $mouvementRepository, DemandeTypeRepository $dm_Repository, DetailBudgetRepository $detailBudgetRepository, CompteMereRepository $compteMereRepository): Response
    {
        $data = $dm_Repository->find($id);          // Find demandeType by this ID
        if (!$data) {
            return new JsonResponse(['success' => false, 'message' => 'Demande introuvable',]);
        }
        $exercice = $data->getExercice();                   // Avoir l'exercice liée au demande
        $solde_debit = $mouvementRepository->soldeDebitParModePaiement($exercice, $data->getDmModePaiement());
        $solde_CREDIT = $mouvementRepository->soldeCreditParModePaiement($exercice, $data->getDmModePaiement());

        $compte_mere = $data->getPlanCompte()->getCompteMere();
        $budget = $detailBudgetRepository->findByExerciceEtCpt($exercice, $compte_mere);

        dump("Solde nivoka= ".$solde_debit);
        dump("Solde niditra= ".$solde_CREDIT);

        if ($budget != null) {
            $budget = $budget->getBudgetMontant();
        }
        if ($solde_debit == null) {
            $solde_reste = 0;
        } else if ($solde_CREDIT == null) {
            $solde_reste = $solde_debit;
        } else {
            $solde_reste = $solde_debit - $solde_CREDIT;
        }

        $solde_reste = 70000;
        $budget = 200000 + 100000 + 100000 + 200000 + 90000 + 500000 + 150000;;
        $depense = 70000 + 100000 + 30000 + 10000 + 50000 + 90000 +75000 +  200000 + 100000 + 100000 + 200000 + 90000 + 500000 + 150000;
        $budget_reste = $budget - $depense;
        return $this->render('sg/valider_demande.html.twig', [
            'demande_type' => $data,
            'solde_reste' => $solde_reste,
            'budget' => $budget,
            //ALANA ITO RANDY
            //'budget_reste' => $budget - $solde_debit,
            'budget_reste' => $budget_reste,
        ]);
    }

    #[Route('/demande/refuser/{id}', name: 'SG.refus_demande_en_attente', methods: ['GET'])]
    public function refuser($id, DemandeTypeRepository $dm_Repository): Response
    {
        $data = $dm_Repository->find($id);
        return $this->render('sg/refuser_demande.html.twig', ['demande_type' => $data]);
    }


    #[Route('/valider_demande/{id}', name: 'valider_demande', methods: ['POST'])]
    
    public function valider_demande($id, LogDemandeTypeRepository $logDemandeTypeRepository): JsonResponse
    {
        $id_user_sg = $this->user->getId(); // mandeha io 
        $rep = $logDemandeTypeRepository->ajoutValidationDemande($id, $id_user_sg);
        $data = json_decode($rep->getContent(), true);
        if ($data['success'] == true) {
            return new JsonResponse(['success' => true, 'message' => 'validation réussi', 'path' => $this->generateUrl('SG.liste_demande_en_attente')]);
        } else {
            return new JsonResponse(['success' => false, 'message' => $data['message']]);
        }
    }

    #[Route('/refuser_demande/{id}', name: 'refuser_demande', methods: ['POST', 'GET'])]
    public function refuser_demande($id, Request $request, LogDemandeTypeRepository $logDemandeTypeRepository): JsonResponse
    {
        $id_user_sg = $this->user->getId();
        $data = json_decode($request->getContent(), true);
        $commentaire_data = $data['commentaire'] ?? null;
        $rep = $logDemandeTypeRepository->ajoutRefuserDemande($id, $id_user_sg, $commentaire_data);     // Transactionnel

        $data = json_decode($rep->getContent(), true);

        if ($data['success'] == true) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Pas de commentaire reçu ',
                'path' => $this->generateUrl('SG.liste_demande_en_attente')
            ]);
        } else {
            return new JsonResponse(['success' => false, 'message' => $data['message']]);
        }

    }
}

