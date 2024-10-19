<?php

namespace App\Controller;

use App\Entity\DemandeType;
use App\Repository\ApprovisionnementPieceRepository;
use App\Repository\BanqueRepository;
use App\Repository\ChequierRepository;
use App\Repository\DemandeTypeRepository;
use App\Repository\DetailBudgetRepository;
use App\Repository\DetailDemandePieceRepository;
use App\Repository\ExerciceRepository;
use App\Repository\LogDemandeTypeRepository;
use App\Repository\MouvementRepository;
use App\Repository\ObservationDemandeRepository;
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
    public function show($id,
                         ObservationDemandeRepository $observationDemandeRepository,
                         MouvementRepository $mouvementRepository, EntityManagerInterface $entityManager,DetailBudgetRepository $detailBudgetRepository, DetailDemandePieceRepository $demandePieceRepository): Response
    {
        $data = $entityManager->find(DemandeType::class, $id);
        $list_img = $demandePieceRepository->findByDemandeType($data);

        $exercice = $data->getExercice();                   // Avoir l'exercice liée au demande
        $solde_debit = $mouvementRepository->soldeDebitParModePaiement($exercice, $data->getDmModePaiement());
        $solde_CREDIT = $mouvementRepository->soldeCreditParModePaiement($exercice, $data->getDmModePaiement());

        $compte_mere = $data->getPlanCompte()->getCompteMere();
        $budget = $detailBudgetRepository->findByExerciceEtCpt($exercice, $compte_mere);
        if($budget!=null){
            $budget = $budget->getBudgetMontant();
        }
        if ($solde_debit == null ) {
            $solde_reste = 0;
        } else if($solde_CREDIT == null){
            $solde_reste = $solde_debit;
        }
        else {
            $solde_reste = $solde_debit - $solde_CREDIT;
        }

        $observations = $observationDemandeRepository->findByRefdemande($data->getRefDemande());
        if ($observations == null) {
            $observations = [];
        }
        return $this->render('tresorier/show.html.twig', [
            'demande_type' => $data,
            'images' => $list_img,
            'solde_reste' => $solde_reste,
            'budget'=>$budget,
            'budget_reste' => $budget-$solde_debit,
            'observations' =>$observations
        ]);
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
        $budget = $detailBudgetRepository->findByExerciceEtCpt($exercice, $compte_mere);
        if ($budget != null) {
            $budget = $budget->getBudgetMontant();
        }
        if ($solde_debit == null ) {
            $solde_reste = 0;
        } else if($solde_CREDIT == null){
            $solde_reste = $solde_debit;
        }
        else {
            $solde_reste = $solde_debit - $solde_CREDIT;
        }
        $liste_banque = $banqueRepository->findAll();
        //dump($solde_debit ."debit".$solde_CREDIT."credit".$solde_reste."reste".$exercice);
        return $this->render('tresorier/deblocker_fond.html.twig',
            [
                'demande_type' => $data, 'solde_reste' => $solde_reste,
                'banques' => $liste_banque,
                'budget' => $budget,
                'budget_reste' => $budget-$solde_debit,
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
        // PARAMETRES
        // $id => ID du demande à débloqué de fonds 
        // $id_user_tresorier = ID qui devrait être un tresorier A VERIFIER APRES
        $rep = $logDemandeTypeRepository->ajoutDeblockageFond($id, $id_user_tresorier,$banque_id,$numero_cheque,$remettant,$beneficiaire); // Déblocage du fonds demandée

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
                                           MouvementRepository  $mouvementRepository,
                                           ExerciceRepository $exerciceRepository,
                                           BanqueRepository $banqueRepository): Response
    {
        $liste_entite = $planCompteRepository->findCompteCaisse();

        $exercice = $exerciceRepository->getExerciceValide();
        $solde_debit = $mouvementRepository->soldeDebitParModePaiement($exercice, "0");
        $solde_CREDIT = $mouvementRepository->soldeCreditParModePaiement($exercice, "0");

        $liste_banque = $banqueRepository->findAll();

        return $this->render('tresorier/demande_approvisionnement.html.twig', [
            'entites' => $liste_entite,
            'banques' => $liste_banque,
            'situation_caisse' => $solde_debit - $solde_CREDIT,
        ]);
    }

    #[Route('/save_approvisionnement', name: 'tresorier.save_approvisionnement', methods: ['POST'])]
    public function save_approvisionnement(Request            $request,
                                           ExerciceRepository $exoRepository,
                                           DemandeTypeService $dmService,
                                            ApprovisionnementPieceRepository $approvisionnementPieceRepository) : JsonResponse
    {


        $id_user_tresorier = $this->user->getId();
        $exercice = $exoRepository->getExerciceValide();


        //$data_parametre = json_decode($request->getContent(), true);

        // les données :
        $plan_cpt_debit_id = $request->request->get('id_plan_compte_debit') ?? null;
        $montant_demande = $request->request->get('dm_montant') ?? null;
        $paiement = $request->request->get('mode_paiement') ?? null;

        // les dates :
        $date_operation = $request->request->get('date_operation') ?? null;
        $date_saisie = $request->request->get('date_saisie') ?? null;

        // insertion d'un approvisionnement
        // Ajout directe de la comptabilisation dans la partie d'insertion
        $response_data = $dmService->insertDemandeTypeAppro($exercice, $plan_cpt_debit_id, $montant_demande, $paiement, $date_saisie, $date_operation, $id_user_tresorier);
        $response_data = json_decode($response_data->getContent(), true);
        if (!$response_data['success']){
            return new JsonResponse([
                'success' => $response_data['success'],
                'message' => $response_data['message'],
                'path' => $this->generateUrl('tresorier.liste_approvisionnement')
            ]);
        }
        //dump($response_data);
        $image = $request->files->get('image') ?? null;
        $is_image = empty($image);
        //dump($is_image);
        if (!$is_image){
            $ref_approvisionnement = $response_data['ref_approvisionnement'];
            $ajout_image = $dmService->uploadImage($image,$this->getParameter('uploads_approvisionnement'));
            $upload_file = $approvisionnementPieceRepository->AjoutPiece($ref_approvisionnement,$ajout_image);
            $response_data = json_decode($upload_file->getContent(), true);
        }
        return new JsonResponse([
            'success' => $response_data['success'],
            'message' => $response_data['message'],
            'path' => $this->generateUrl('tresorier.liste_approvisionnement')
        ]);
        //return $this->redirectToRoute('tresorier.form_approvisionnement');
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
