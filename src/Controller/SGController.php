<?php

namespace App\Controller;

use App\Repository\CompteMereRepository;
use App\Repository\DemandeTypeRepository;
use App\Repository\DetailBudgetRepository;
use App\Repository\DetailDemandePieceRepository;
use App\Repository\LogDemandeTypeRepository;
use App\Repository\MouvementRepository;
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

        $liste_demande_a_modifier = $dm_Repository->findByEtat(100);
        return $this->render('sg/index.html.twig', [//'demande_types' => $dm_Repository->findAll()->where($dm_Repository == 10)
            'demande_types' => $dm_Repository->findByEtat(100)]);
    }


    #[Route('/demande/modifier/{id}', name: 'SG.modifier_en_attente', methods: ['GET'])]
    public function modifier($id, DemandeTypeRepository $dm_Repository, DetailDemandePieceRepository $demandePieceRepository, MouvementRepository $mouvementRepository, DetailBudgetRepository $detailBudgetRepository, CompteMereRepository $compteMereRepository): Response
    {
        $data = $dm_Repository->find($id);
        $list_img = $demandePieceRepository->findByDemandeType($data);

        $exercice = $data->getExercice();
        $cpt = $data->getPlanCompte()->getCompteMere();

        // $cpt = $compteMereRepository->find(2);
        //$budget = $detailBudgetRepository->findByExerciceEtCpt($exercice, $cpt);
        //$solde = 200;
        //$solde_reste = $budget->getBudgetMontant() - $solde;
        //$solde_reste = $solde;
        //$exerice = $exerciceRepository->find(41);
        //$cpt_mere = $compteMereRepository->find(42);


        $solde_debit = $mouvementRepository->soldeDebitByExerciceByCompteMere($exercice, $cpt);
        $solde_CREDIT = $mouvementRepository->soldeCreditByExerciceByCompteMere($exercice, $cpt);

        return $this->render('sg/modifier_demande.html.twig', ['demande_type' => $data, 'images' => $list_img, 'solde_reste' => ($solde_debit - $solde_CREDIT)]);
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
            return new JsonResponse(['success' => true, 'message' => 'Pas de commentaire reçu ', 'path' => $this->generateUrl('SG.liste_demande_en_attente')]);
        } else {
            return new JsonResponse(['success' => false, 'message' => $data['message']]);
        }
    }

    #[Route('/{id}', name: 'SG.detail_demande_en_attente', methods: ['GET'])]
    public function show($id, MouvementRepository $mouvementRepository, DemandeTypeRepository $dm_Repository, DetailDemandePieceRepository $demandePieceRepository, DetailBudgetRepository $detailBudgetRepository, CompteMereRepository $compteMereRepository): Response
    {
        $data = $dm_Repository->find($id);
        $list_img = $demandePieceRepository->findByDemandeType($data);

        $exercice = $data->getExercice();
        $cpt = $data->getPlanCompte()->getCompteMere();

        // $cpt = $compteMereRepository->find(2);
        //$budget = $detailBudgetRepository->findByExerciceEtCpt($exercice, $cpt);
        //$solde = 200;
        //$solde_reste = $budget->getBudgetMontant() - $solde;
        //$solde_reste = $solde;

        $solde_debit = $mouvementRepository->soldeDebitByExerciceByCompteMere($exercice, $cpt);
        $solde_CREDIT = $mouvementRepository->soldeCreditByExerciceByCompteMere($exercice, $cpt);


        $mode_paiement = $data->getDmModePaiement();
        if ($mode_paiement == 0) {
            $data->setDmModePaiement("éspèce");
        } else if ($mode_paiement == 1) {
            $data->setDmModePaiement("Chèque");
        }
        $data->setDmModePaiement($mode_paiement);
        return $this->render('sg/show.html.twig', ['demande_type' => $data, 'images' => $list_img, 'solde_reste' => ($solde_debit - $solde_CREDIT)]);
    }

    #[Route('/demande/valider/{id}', name: 'SG.valider_en_attente', methods: ['GET'])]
    public function valider($id, DemandeTypeRepository $dm_Repository, DetailBudgetRepository $detailBudgetRepository, CompteMereRepository $compteMereRepository): Response
    {
        $data = $dm_Repository->find($id);          // Find demandeType by this ID
        if (!$data) {
            return new JsonResponse(['success' => false, 'message' => 'Demande introuvable',]);
        }
        $exercice = $data->getExercice();                   // Avoir l'exercice liée au demande
        $cpt = $data->getPlanCompte()->getCompteMere();     // Avoir le compteMere du Motif liéé au DemandeType
        $budget = $detailBudgetRepository->findByExerciceEtCpt($exercice, $cpt);
        $solde = 200;
        $solde_reste = $solde;
        return $this->render('sg/valider_demande.html.twig', ['demande_type' => $data, 'solde_reste' => $solde_reste]);
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

        $rep = $logDemandeTypeRepository->ajoutRefuserDemande($id, $id_user_sg, $commentaire_data);

        $data = json_decode($rep->getContent(), true);

        if ($data['success'] == true) {
            return new JsonResponse(['success' => true, 'message' => 'Pas de commentaire reçu ', 'path' => $this->generateUrl('SG.liste_demande_en_attente')]);
        } else {
            return new JsonResponse(['success' => true, 'message' => 'Pas de commentaire reçu ']);
        }

    }
}

