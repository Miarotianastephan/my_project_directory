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
        $requestURI = $request->getRequestUri();
        $allFilters = [
            'initie' => false,
            'attente_modif' => false,
            'modifier' => false,
            'attente_fond' => false,
            'attente_versement' => false,
            'attente_refuser' => false,
            'debloquer' => false,
            'refuser' => false,
            'reverser' => false,
            'comptabiliser' => false
        ];
        if ($requestURI == "/commisaire/") {
            $allFilters = array_fill_keys(array_keys($allFilters), true);
        } else {
            foreach ($allFilters as $filter => &$active) {
                if (str_contains($requestURI, "value=$filter") || str_contains($requestURI, $filter)) {
                    $active = true;
                }
            }
        }
        $exercice_valide = $exerciceRepository->getExerciceValide();
        $demande_types = $demandeTypeRepository->findActiveByExercice($exercice_valide, $allFilters);
        return $this->render('commisaire_compte/index.html.twig', [
            'exercice' => $exercice_valide,
            'demande_types' => $demande_types,
            'filters' => $allFilters,
        ]);
    }

    #[Route('/approvisionnement', name: 'app_commisaire_approvisionnement')]
    public function list_approvisionnement(): Response
    {
        return $this->render('commisaire_compte/show.html.twig', []);
    }

    #[Route('/demande/{id}', name: 'app_commisaire_show')]
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
