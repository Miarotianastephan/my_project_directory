<?php

namespace App\Controller;

use App\Repository\DemandeTypeRepository;
use App\Repository\DetailDemandePieceRepository;
use App\Repository\PlanCompteRepository;
use App\Service\CompteService;
use App\Service\DemandeTypeService;
use App\Service\ExerciceService;
use CompteHierarchyService;
use CompteIndex;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DemandeurController extends AbstractController
{

    private $plan_cpt_repo;

    
    public function __construct(PlanCompteRepository $planCptRepo)
    {
        $this->plan_cpt_repo = $planCptRepo;
    }
    

    #[Route('/demandeur', name: 'demandeur.liste_demande')]
    public function index(DemandeTypeService $dmService): Response
    {
        // Lister mes demandes
        $mes_demandes = $dmService->findAllMyDemandeTypesInit();
        $mylogs = $dmService->findAllMyDemande();
        return $this->render('demandeur/demandeur.html.twig',[
            'demandes' => $mes_demandes,
            'logs_demandes' => $mylogs
        ]);
    }

    #[Route('/demandeur/form', name: 'demandeur.nouveau_demande')]
    public function addNewDemandeForm(): Response
    {
        $data_compte_depense = $this->plan_cpt_repo->findCompteDepense();
        $data_entity = $this->plan_cpt_repo->findEntityCode();
        return $this->render('demandeur/demandeur_add.html.twig',[
            'data_compte_depense' => $data_compte_depense,
            'data_entity' => $data_entity
        ]);
    }

    #[Route(path: '/demandeur/add', name: 'demandeur.save_nouveau_demande', methods: ['POST'])]
    public function addNewDemandeFormAction(Request $request, DemandeTypeService $dmService, ExerciceService $exoService){
        // getExerciceId 
        $exercice = $exoService->getLastExercice();
        $data_parametre = $request->request->all();
        
        // les données :
        $plan_cpt_entity_id = $data_parametre['id_plan_comptable_entity'];
        $plan_cpt_motif_id = $data_parametre['id_plan_comptable_motif'];
        $montant_demande = $data_parametre['dm_montant'];
        $paiement = $data_parametre['mode_paiement'];

        // les dates :
        $date_operation = $data_parametre['date_operation'];
        $date_saisie = $data_parametre['date_saisie'];
        
        $response_data = $dmService->insertDemandeType($exercice, $plan_cpt_entity_id, $plan_cpt_motif_id, $montant_demande, $paiement,$date_saisie , $date_operation);
        dump($response_data);
        return $this->redirectToRoute('demandeur.liste_demande');
    }

    // get by ID
    #[Route('/demandeur/{id}', name: 'demandeur.detail_demande_en_attente', methods: ['GET'])]
    public function show($id, DemandeTypeRepository $dm_Repository, DetailDemandePieceRepository $demandePieceRepository): Response
    {
        $data = $dm_Repository->find($id);
        $list_img = $demandePieceRepository->findByDemandeType($data);
        dump(count($list_img)); 
        return $this->render('/demandeur/demandeur_show.html.twig',
            ['demande_type' => $data, 'images' => $list_img]
        );
    }

}
