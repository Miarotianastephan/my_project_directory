<?php

namespace App\Controller;

use App\Entity\DemandeType;
use App\Repository\BanqueRepository;
use App\Repository\ChequierRepository;
use App\Repository\DemandeTypeRepository;
use App\Repository\DetailDemandePieceRepository;
use App\Repository\LogDemandeTypeRepository;
use App\Repository\MouvementRepository;
use App\Repository\PlanCompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    public function valider_fond($id, MouvementRepository $mouvementRepository, DemandeTypeRepository $dm_type): Response
    {
        $data = $dm_type->find($id);
        //$montant = 111;

        $exercice = $data->getExercice();
        $cpt = $data->getPlanCompte()->getCompteMere();


        $solde_debit = $mouvementRepository->soldeDebitByExerciceByCompteMere($exercice, $cpt);
        $solde_CREDIT = $mouvementRepository->soldeCreditByExerciceByCompteMere($exercice, $cpt);

        return $this->render('tresorier/deblocker_fond.html.twig',
            ['demande_type' => $data, 'montant' => ($solde_debit - $solde_CREDIT)]
        );
    }

    #[Route('/remettre_fond/{id}', name: 'tresorier.remettre_fond', methods: ['POST'])]
    public function remettre_fond($id, LogDemandeTypeRepository $logDemandeTypeRepository): JsonResponse
    {
        $id_user_tresorier = $this->user->getId();
        // $id => ID du demande à débloqué de fonds 
        // $id_user_tresorier = ID qui devrait être un tresorier A VERIFIER APRES
        $rep = $logDemandeTypeRepository->ajoutDeblockageFond($id, $id_user_tresorier); // Déblocage du fonds demandée
        // O_COMPTA
        // à compléter
        // fin O_COMPTA

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


    #[Route('/demande_approvisionnement', name: 'tresorier.form_approvisionnement', methods: ['GET'])]
    public function form_approvisionnement(PlanCompteRepository $planCompteRepository,
                                           MouvementRepository  $mouvementRepository, BanqueRepository $banqueRepository): Response
    {
        $liste_entite = $planCompteRepository->findCompteCaisse();

        // $solde_debit = $mouvementRepository->soldeDebitByExerciceByCompteMere($exercice, $cpt);
        // $solde_CREDIT = $mouvementRepository->soldeCreditByExerciceByCompteMere($exercice, $cpt);
        $liste_banque = $banqueRepository->findAll();

        return $this->render('tresorier/demande_approvisionnement.html.twig', [
            'entites' => $liste_entite,
            'banques' => $liste_banque,
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
                'url' => $this->generateUrl('tresorier.form_approvisionnement')
            ]
        );
    }

}
