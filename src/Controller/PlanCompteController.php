<?php

namespace App\Controller;

use App\Repository\PlanCompteRepository;
use App\Service\PlanCompteService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/plan')]
class PlanCompteController extends AbstractController
{
    #[Route('/compte', name: 'app_plan_compte')]
    public function index(PlanCompteRepository $planCompte): Response
    {
        $data_plan_compte = $planCompte->findAll();
        return $this->render('plan_compte/plan_compte.html.twig',[
            'data_plan_compte' => $data_plan_compte,
        ]);
    }

    #[Route(path: '/import', name: 'app_plan_compte.import', methods: ['GET'])]
    public function importPlanCompte(){
        return $this->render('plan_compte/plan_compte_import.html.twig');
    }

    #[Route(path: '/add', name: 'app_plan_compte.add', methods: ['GET'])]
    public function ajouterPlanCompte(){
        return $this->render('plan_compte/plan_compte_add.html.twig');
    }

    #[Route(path: '/import/save', name: 'app_plan_compte.save', methods: ['POST'])]
    public function sauvegarderImportPlanCompte(Request $request, PlanCompteService $planCompteService,EntityManagerInterface $entityManager){
        $data = json_decode($request->getContent(), true);
        foreach($data as $key => $compte){
            $planCompteService->insertHierarchy($entityManager, $compte);
        }
    }

}
