<?php

namespace App\Controller;

use App\Service\CompteService;
use CompteHierarchyService;
use CompteIndex;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DemandeurController extends AbstractController
{
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
        return $this->render('demandeur/demandeur_add.html.twig');
    }

    #[Route(path: '/demandeur/add', name: 'demandeur.save_nouveau_demande', methods: ['POST'])]
    public function addNewDemandeFormAction(Request $request){

    }

}
