<?php

namespace App\Controller;

use App\Repository\PlanCompteRepository;
use App\Service\CompteService;
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
    public function addNewDemandeFormAction(Request $request){

    }

}
