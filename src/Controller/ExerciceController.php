<?php

namespace App\Controller;

use App\Repository\ExerciceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/exercice')]
class ExerciceController extends AbstractController
{
    /**
     * Page de liste des exercices
     * @param ExerciceRepository $exerciceRepository
     * @return Response
     */
    #[Route('/', name: 'app_exercice')]
    public function index(ExerciceRepository $exerciceRepository): Response
    {
        return $this->render('exercice/index.html.twig', ['exercices' => $exerciceRepository->findAll()]);
    }

    /**
     * Page d'ajout d'exercice :
     *
     * @param ExerciceRepository $exerciceRepository
     * @return Response
     */
    #[Route('/ajout', name: 'app_form_ajout')]
    public function form_ajout(): Response
    {
        return $this->render('exercice/form_ajout.html.twig');
    }

    /**
     * Détails d'exercice
     *
     * @param $id
     * @param ExerciceRepository $exerciceRepository
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'app_find_exercie', methods: ['GET'])]
    public function findExercice($id, ExerciceRepository $exerciceRepository): JsonResponse
    {
        $exercice = $exerciceRepository->find($id);
        if (!$exercice) {
            return new JsonResponse(['success' => false, 'message' => "Exercice introuvable"], 404);
        }

        return new JsonResponse([
            'success' => true,
            'id' => $exercice->getId(),
            'ExerciceDateDebut' => $exercice->getExerciceDateDebut()->format('Y-m-d'),
            'ExerciceDateFin' => $exercice->getExerciceDateFin() ? $exercice->getExerciceDateFin()->format('Y-m-d') : null
        ]);
    }

    /**
     * Enregistrement d'exercice au niveau de la base de donnée
     *
     * @param Request $request
     * @param ExerciceRepository $exerciceRepository
     * @return JsonResponse
     */
    #[Route('/ajout_exercice', name: 'app_ajout', methods: ['POST'])]
    public function ajout(Request $request, ExerciceRepository $exerciceRepository): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        $date_debut = $data['date_debut'] ?? null;
        if (!$date_debut) {
            return new JsonResponse(['success' => false, 'message' => "Date de début nécessaire"]);

        }
        $date_fin = $data['date_fin'] ?? null;

        $addbase = $exerciceRepository->ajoutExercice($date_debut, $date_fin);

        $addbase = json_decode($addbase->getContent(), true);
        return new JsonResponse(
            [
                'success' => $addbase['success'],
                'message' => $addbase['message'],
                'url' => $this->generateUrl("app_exercice")
            ]
        );

    }

    /**
     * Cloture d'un exercice à partir de la page de list des exercices.
     * Il est impossible de cloturé un exercice qui n'est pas ouvert.
     *
     * @param $id
     * @param Request $request
     * @param ExerciceRepository $exerciceRepository
     * @return JsonResponse
     */
    #[Route('/cloturer/{id}', name: 'app_cloturer_exercie', methods: ['POST'])]
    public function cloturerExercice($id, Request $request, ExerciceRepository $exerciceRepository): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        $date_fin = $data['date_fin'] ?? null;

        $addbase = $exerciceRepository->cloturerExercice($id, $date_fin);
        $addbase = json_decode($addbase->getContent(), true);

        return new JsonResponse([
            'success' => $addbase['success'],
            'message' => $addbase['message'],
        ]);
    }

    /**
     * Enregistrement d'une ouverture d'exercice.
     * S'il y a déja un exercice en cours, il sera impossible d'ouvrir un autre exercice.
     * Alors la controlleur retournera un message d'erreur.
     *
     * @param $id
     * @param Request $request
     * @param ExerciceRepository $exerciceRepository
     * @return JsonResponse
     */
    #[Route('/ouverture/{id}', name: 'app_ouverture_exercie', methods: ['POST'])]
    public function ouvertureExercice($id, Request $request, ExerciceRepository $exerciceRepository): JsonResponse
    {

        $exercice = $exerciceRepository->getExerciceValide();
        if ($exercice) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Fermez tous les exercices avant d\'en ouvrir',
            ]);
        }
        $addbase = $exerciceRepository->ouvertureExercice($id);
        $addbase = json_decode($addbase->getContent(), true);

        return new JsonResponse([
            'success' => $addbase['success'],
        ]);
    }
}
