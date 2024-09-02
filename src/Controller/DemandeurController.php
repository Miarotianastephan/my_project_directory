<?php

namespace App\Controller;

use App\Service\CompteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DemandeurController extends AbstractController
{
    #[Route('/demandeur', name: 'demandeur.liste_demande')]
    public function index(CompteService $compteService): Response
    {
        $list = $compteService->constructionhirarchie();
        $compteService->afficherHierarchie($list);
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
