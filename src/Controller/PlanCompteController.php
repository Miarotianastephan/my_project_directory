<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/plan')]
class PlanCompteController extends AbstractController
{
    #[Route('/compte', name: 'app_plan_compte')]
    public function index(): Response
    {
        return $this->render('plan_compte/plan_compte.html.twig');
    }

    #[Route(path: '/import', name: 'app_plan_compte.import', methods: ['GET'])]
    public function importPlanCompte(){
        return $this->render('plan_compte/plan_compte_import.html.twig');
    }

    #[Route(path: '/add', name: 'app_plan_compte.add', methods: ['GET'])]
    public function ajouterPlanCompte(){
        return $this->render('plan_compte/plan_compte_add.html.twig');
    }
}
