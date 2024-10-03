<?php

namespace App\Controller;

use App\Repository\MouvementRepository;
use App\Repository\VDemandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/mouvement', name: 'app_mouvement')]
class MouvementController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(): Response
    {
        return $this->render('mouvement/index.html.twig', [
            'controller_name' => 'MouvementController',
        ]);
    }

    #[Route('/test_view', name: '_view')]
    public function v_mouvement(VDemandeRepository $demandeRepository): JsonResponse
    {
        return  new JsonResponse($demandeRepository->findAll());
    }
}
