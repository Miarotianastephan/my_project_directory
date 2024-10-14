<?php

namespace App\Controller;

use App\Repository\DemandeTypeRepository;
use App\Repository\DetailDemandePieceRepository;
use App\Repository\ExerciceRepository;
use App\Repository\LogDemandeTypeRepository;
use App\Repository\ObservationDemandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/commisaire')]
class CommisaireCompteController extends AbstractController
{
    #[Route('/', name: 'app_commisaire_compte')]
    public function index(Request               $request,
                          ExerciceRepository    $exerciceRepository,
                          DemandeTypeRepository $demandeTypeRepository): Response
    {
        $exercice_valide = $exerciceRepository->getExerciceValide();
        $demande_types = $demandeTypeRepository->findByExercice($exercice_valide);
        return $this->render('commisaire_compte/index.html.twig', [
            'exercice' => $exercice_valide,
            'demande_types' => $demande_types,
        ]);
    }

    #[Route('/{id}', name: 'app_commisaire_show')]
    public function show($id,
                         DemandeTypeRepository $demandeTypeRepository,
                         DetailDemandePieceRepository $detailDemandePieceRepository,
                         ObservationDemandeRepository $observationDemandeRepository,
                         LogDemandeTypeRepository $logDemandeTypeRepository): Response
    {
        $demande_type = $demandeTypeRepository->find($id);
        $list_img = $detailDemandePieceRepository->findByDemandeType($demande_type);
        $observations = $observationDemandeRepository->findByRefdemande($demande_type->getRefdemande());
        $historique = $logDemandeTypeRepository->findByDemandeType($demande_type);
        return $this->render('commisaire_compte/show.html.twig', [
            'demande_type' => $demande_type,
            'images' => $list_img,
            'observations' => $observations,
            'historiques' => $historique,
        ]);
    }
}