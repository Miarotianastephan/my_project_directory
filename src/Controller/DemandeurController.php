<?php

namespace App\Controller;

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
    public function index(): Response
    {
        // Récupération des plans de comptes
        $rawData = [
            ['N° de compte' => '67', 'Intitullé' => 'Matériels de bureau'],
            ['N° de compte' => '67900', 'Intitullé' => 'Compte d\'attente à régulariser'],
            ['N° de compte' => '66', 'Intitullé' => 'Matériels de bureau'],
            ['N° de compte' => '661', 'Intitullé' => 'Compte d\'attente à régulariser'],
            ['N° de compte' => '661100', 'Intitullé' => 'Matériels de bureau 2'],
            ['N° de compte' => '661120', 'Intitullé' => 'Matériels de bureau 3'],
            ['N° de compte' => '330000', 'Intitullé' => 'TEST SANS MERE'],
            // ... Ajoutez le reste des données ici
        ];
        $compteHierarchy = new CompteIndex($rawData);
        // $hierarchy = $compteHierarchy->getHierarchy();
        
        // Affichage de la hiérarchie (à des fins de démonstration)
        // dump($hierarchy);
        return $this->render('demandeur/demandeur.html.twig');
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
        $exercice_id = $exoService->getLastExercice();
        $data_parametre = $request->request->all();
        
        // les données :
        $plan_cpt_entity_id = $data_parametre['id_plan_comptable_entity'];
        $plan_cpt_motif_id = $data_parametre['id_plan_comptable_motif'];
        $montant_demande = $data_parametre['dm_montant'];
        $paiement = $data_parametre['mode_paiement'];
        
        // les dates :
        $date_operation = $data_parametre['date_operation'];
        $date_saisie = $data_parametre['date_saisie'];
        
        $dmService->insertDemandeType($exercice_id, $plan_cpt_entity_id, $plan_cpt_motif_id, $montant_demande, $paiement);

        return $this->redirectToRoute('demandeur.nouveau_demande');
    }

}
