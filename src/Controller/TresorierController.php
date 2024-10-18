<?php

namespace App\Controller;

use App\Entity\DemandeType;
use App\Repository\BanqueRepository;
use App\Repository\ChequierRepository;
use App\Repository\DemandeRepository;
use App\Repository\DemandeTypeRepository;
use App\Repository\DetailBudgetRepository;
use App\Repository\DetailDemandePieceRepository;
use App\Repository\EtatDemandeRepository;
use App\Repository\EvenementRepository;
use App\Repository\ExerciceRepository;
use App\Repository\LogDemandeTypeRepository;
use App\Repository\MouvementRepository;
use App\Repository\ObservationDemandeRepository;
use App\Repository\PlanCompteRepository;
use App\Repository\VersementsRepository;
use App\Service\DemandeTypeService;
use App\Service\OperationInverseService;
use App\Service\VersementService;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\Evenement;
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
            'demande_types' => $dm_Repository->findByEtat(null,[200,202])
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
                                           DemandeTypeService $dmService) : JsonResponse
    {

        $id_user_tresorier = $this->user->getId();
        $exercice = $exoRepository->getExerciceValide();
        //$data_parametre = $request->request->all();


        $data_parametre = json_decode($request->getContent(), true);

        // les données :
        $plan_cpt_debit_id = $data_parametre['id_plan_compte_debit'] ?? null;
        $montant_demande = $data_parametre['dm_montant'] ?? null;
        $paiement = $data_parametre['mode_paiement'] ?? null;

        // les dates :
        $date_operation = $data_parametre['date_operation'] ?? null;
        $date_saisie = $data_parametre['date_saisie'] ?? null;
        // insertion d'un approvisionnement
        // Ajout directe de la comptabilisation dans la partie d'insertion
        $response_data = $dmService->insertDemandeTypeAppro($exercice, $plan_cpt_debit_id, $montant_demande, $paiement, $date_saisie, $date_operation, $id_user_tresorier);
        //dump($response_data);

        $response_data = json_decode($response_data->getContent(), true);
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

    // Versement de fonds
    #[Route('/reverser/form/{id}', name: 'tresorier.reveser_demande_en_attente', methods: ['GET'])]
    public function reversement($id, EntityManagerInterface $entityManager, EvenementRepository $evnRepository, MouvementRepository $mvtRepository): Response
    {
        $data = $entityManager->find(DemandeType::class, $id);
        $ref_demande = $data->getRefDemande();
        $evenement = $evnRepository->findByEvnReference($ref_demande);
        $listMouvement = $mvtRepository->findAllMvtByEvenement($evenement);
        // $listMouvement = $mvtRepository->findAllMvtByEvenement($evenement);
        return $this->render('tresorier/versement_fond.html.twig', [
            'info_demande' => $data,
            'info_mouvement' => $listMouvement
        ]);
    }

    #[Route('/reverser/save', name: 'tresorier.reverser_demande_en_attente_save', methods: ['POST'])]
    public function reversementSave(Request $request, EtatDemandeRepository $etatDemandeRepository, OperationInverseService $operationInvService, VersementsRepository $versementRepository, VersementService $vrsmService, DemandeTypeRepository $demandeTypeRepository, EvenementRepository $evnRepository, MouvementRepository $mvtRepository)
    {
        $data = $request->request->all();
        $demande = $demandeTypeRepository->find($data['dm_id']);
        $utilisateur = $this->user;
        $entityManager = $versementRepository->getEntityManager();
        $entityManager->beginTransaction();
        try {
            $montant_verser = $data['dm_montant_verser'];
            $vrsm = $versementRepository->persistVersement($entityManager, $data['nom_remettant'],new DateTime($data['date_operation']),$data['adresse'],$montant_verser,$demande,$utilisateur,$data['motif_versement']);
            $vrsm_reference = $vrsmService->createReferenceForVersementId($vrsm->getId());
            $vrsm->setVrsmReference($vrsm_reference); //creation reference
            $entityManager->flush();

            // findEvenementByReference 
            dump("FIND VERSEMENT");
            $ref_demande = $demande->getRefDemande();
            $evenement = $evnRepository->findByEvnReference($ref_demande);
            $vrsm_evenement = $evnRepository->persistEvenement($entityManager,$evenement->getEvnTrsId(),$utilisateur,$evenement->getEvnExercice(),$evenement->getEvnCodeEntity(),$montant_verser,$vrsm->getVrsmReference(),new DateTime());

            $listMouvement = $mvtRepository->findAllMvtByEvenement($evenement);
            // création des mouvements inverse
            $operationInvService->inverseTransaction($vrsm_evenement,$entityManager,$listMouvement,$montant_verser);
            
            // mis à jour du demandes en etat versée
            $demande->setDmEtat($etatDemandeRepository, 401);
            $entityManager->flush();
            $entityManager->commit();
        } catch (\Throwable $th) {
            $entityManager->rollback();
            dump($th);
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur versement: ' . $th->getMessage()
            ]);
        }
        return $this->redirectToRoute('tresorier.liste_demande_en_attente');
    }
    

}
