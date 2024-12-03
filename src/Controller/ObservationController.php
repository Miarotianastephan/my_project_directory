<?php

namespace App\Controller;

use App\Repository\ObservationDemandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/observation')]
class ObservationController extends AbstractController
{
    private $user;

    public function __construct(Security $security)
    {
        // $this->security = $security;
        $this->user = $security->getUser();
    }

    #[Route('/', name: 'app_observation')]
    public function index(): Response
    {
        return $this->render('observation/index.html.twig', [
            'controller_name' => 'ObservationController',
        ]);
    }

    /**
     * Sauvegarde d'observation d'une demande.
     *
     * @param Request $request
     * @param ObservationDemandeRepository $observationDemandeRepository
     * @return JsonResponse
     */
    #[Route('/ajout', name: 'app_ajout_observation', methods: ['POST'])]
    public function ajout(Request $request, ObservationDemandeRepository $observationDemandeRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ref_demande = $data['ref_demande'] ?? null;
        $observation = $data['observation'] ?? null;
        $user_matricule = $this->user->getUserMatricule();

        $rep = $observationDemandeRepository->ajoutObservation($ref_demande, $user_matricule,$observation);
        $rep = json_decode($rep->getContent(), true);
        return new JsonResponse([
            'success' => $rep['success'],
            'message' => $rep['message'],
        ]);

    }
}
