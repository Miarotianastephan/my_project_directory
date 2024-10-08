<?php

namespace App\Controller;

use App\Entity\DemandeType;
use App\Repository\BanqueRepository;
use App\Repository\ChequierRepository;
use App\Repository\DemandeTypeRepository;
use App\Repository\DetailBudgetRepository;
use App\Repository\DetailDemandePieceRepository;
use App\Repository\ExerciceRepository;
use App\Repository\LogDemandeTypeRepository;
use App\Repository\MouvementRepository;
use App\Repository\PlanCompteRepository;
use App\Service\DemandeTypeService;
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
    public function show($id, MouvementRepository $mouvementRepository, EntityManagerInterface $entityManager,DetailBudgetRepository $detailBudgetRepository, DetailDemandePieceRepository $demandePieceRepository): Response
    {
        $data = $entityManager->find(DemandeType::class, $id);
        $list_img = $demandePieceRepository->findByDemandeType($data);

        $exercice = $data->getExercice();                   // Avoir l'exercice liée au demande
        $solde_debit = $mouvementRepository->soldeDebitParModePaiement($exercice, $data->getDmModePaiement());
        $solde_CREDIT = $mouvementRepository->soldeCreditParModePaiement($exercice, $data->getDmModePaiement());

        $compte_mere = $data->getPlanCompte()->getCompteMere();
        $budget = $detailBudgetRepository->findByExerciceEtCpt($exercice, $compte_mere)->getBudgetMontant();
        if ($solde_debit == null || $solde_CREDIT == null) {
            $solde_reste = 0;
        } else {
            $solde_reste = $solde_debit - $solde_CREDIT;
        }
        return $this->render('tresorier/show.html.twig', ['demande_type' => $data, 'images' => $list_img, 'solde_reste' => $solde_reste,'budget'=>$budget]);
    }

    #[Route('/demande/valider/{id}', name: 'tresorier.valider_fond', methods: ['GET'])]
    public function valider_fond($id,
                                 MouvementRepository $mouvementRepository,
                                 BanqueRepository $banqueRepository,
                                 DemandeTypeRepository $dm_type,DetailBudgetRepository $detailBudgetRepository): Response
    {
        $data = $dm_type->find($id);

        $exercice = $data->getExercice();                   // Avoir l'exercice liée au demande
        $solde_debit = $mouvementRepository->soldeDebitParModePaiement($exercice, $data->getDmModePaiement());
        $solde_CREDIT = $mouvementRepository->soldeCreditParModePaiement($exercice, $data->getDmModePaiement());

        $compte_mere = $data->getPlanCompte()->getCompteMere();
        $budget = $detailBudgetRepository->findByExerciceEtCpt($exercice, $compte_mere)->getBudgetMontant();
        if ($solde_debit == null || $solde_CREDIT == null) {
            $solde_reste = 0;
        } else {
            $solde_reste = $solde_debit - $solde_CREDIT;
        }
        $liste_banque = $banqueRepository->findAll();

        return $this->render('tresorier/deblocker_fond.html.twig',
            [
                'demande_type' => $data, 'solde_reste' => $solde_reste,
                'banques' => $liste_banque,
                'budget' => $budget,
            ]
        );
    }

    #[Route('/remettre_fond/{id}', name: 'tresorier.remettre_fond', methods: ['POST'])]
    public function remettre_fond(Request                  $request,
                                                           $id,
                                  LogDemandeTypeRepository $logDemandeTypeRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $banque_id = $data['banque'] ?? null;
        $numero_cheque = $data['numero_cheque'] ?? null;
        $beneficiaire = $data['beneficiaire'] ?? null;
        $remettant = $data['remettant'] ?? null;
        $id_user_tresorier = $this->user->getId();
        // $id => ID du demande à débloqué de fonds 
        // $id_user_tresorier = ID qui devrait être un tresorier A VERIFIER APRES
        $rep = $logDemandeTypeRepository->ajoutDeblockageFond($id, $id_user_tresorier,$banque_id,$numero_cheque,$remettant,$beneficiaire); // Déblocage du fonds demandée
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
            'situation_caisse' => 10
        ]);
    }

    #[Route('/save_approvisionnement', name: 'tresorier.save_approvisionnement', methods: ['POST'])]
    public function save_approvisionnement(Request            $request,
                                           ExerciceRepository $exoRepository,
                                           DemandeTypeService $dmService)
    {

        $id_user_tresorier = $this->user->getId();
        $exercice = $exoRepository->getExerciceValide();
        $data_parametre = $request->request->all();

        // les données :
        $plan_cpt_debit_id = $data_parametre['id_plan_compte_debit'];
        $montant_demande = $data_parametre['dm_montant'];
        $paiement = $data_parametre['mode_paiement'];

        // les dates :
        $date_operation = $data_parametre['date_operation'];
        $date_saisie = $data_parametre['date_saisie'];
        // insertion d'un approvisionnement
        // Ajout directe de la comptabilisation dans la partie d'insertion
        $response_data = $dmService->insertDemandeTypeAppro($exercice, $plan_cpt_debit_id, $montant_demande, $paiement, $date_saisie, $date_operation, $id_user_tresorier);
        dump($response_data);
        return $this->redirectToRoute('tresorier.form_approvisionnement');
    }

    #[Route('/liste_approvisionnement', name: 'tresorier.liste_approvisionnement', methods: ['GET'])]
    public function liste_approvisionnement(DemandeTypeRepository $demandeRepository): Response
    {
        $liste_approvisio = $demandeRepository->findAllAppro();
        return $this->render('tresorier/liste_approvisionnement.html.twig', [
            'liste_approvisio' => $liste_approvisio,
        ]);
    }
    

}
