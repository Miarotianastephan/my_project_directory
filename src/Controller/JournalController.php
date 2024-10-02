<?php

namespace App\Controller;

use App\Repository\ExerciceRepository;
use App\Repository\MouvementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/journal')]
class JournalController extends AbstractController
{
    #[Route('/', name: 'app_journal')]
    public function index(): Response
    {
        return $this->render('journal/index.html.twig', [
            'controller_name' => 'JournalController',
        ]);
    }

    #[Route('/livre_journal', name: 'livre-journal')]
    public function livre_journal(MouvementRepository $mouvementRepository, ExerciceRepository $exerciceRepository): Response
    {
        $exercice = $exerciceRepository->getExerciceValide();
        if (!$exercice) {
            return new Response("Aucun exercice ouvert");

        }
        //$list_evenement = $mouvementRepository->findAll();
        $list_evenement = $mouvementRepository->findByExercice($exercice);
        return $this->render('journal/journal_caisse.html.twig', ['mouvements' => $list_evenement]);
    }

    #[Route('/exercice/journal', name: 'exercice-journal')]
    public function livre_journal_exercice(MouvementRepository $mouvementRepository, ExerciceRepository $exerciceRepository): JsonResponse
    {
        $exercice = $exerciceRepository->getExerciceValide();
        if (!$exercice) {
            return new JsonResponse(['success' => false, 'message' => "Aucun exercice ouvert"]);

        }
        $list_evenement = $mouvementRepository->findByExercice($exercice);
        return new JsonResponse(['success' => true, 'data' => $list_evenement]);


    }
}
