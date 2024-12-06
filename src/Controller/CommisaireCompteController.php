<?php

namespace App\Controller;

use App\Repository\ApprovisionnementPieceRepository;
use App\Repository\DemandeRepository;
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
    /**
     * Affiche la liste des demandes de décaissement de fonds pour l'exercice actuellement ouvert/actif.
     * Les demandes sont filtrées selon l'URL.
     * Si l'URL est "/commisaire", tous les filtres sont actifs.
     * Sinon, les filtres actifs sont spécifiés dans l'URL.
     *
     * @param Request $request La requête HTTP
     * @param ExerciceRepository $exerciceRepository Le repository pour récupérer l'exercice valide
     * @param DemandeTypeRepository $demandeTypeRepository Le repository pour récupérer les types de demandes
     * @param DemandeRepository $demandeRepository Le repository pour récupérer les demandes
     *
     * @return Response La réponse contenant la vue de la liste des demandes filtrées
     */
    #[Route('/', name: 'app_commisaire_compte')]
    public function index(Request               $request,
                          ExerciceRepository    $exerciceRepository,
                          DemandeTypeRepository $demandeTypeRepository,
                          DemandeRepository     $demandeRepository): Response
    {
        $requestURI = $request->getRequestUri();
        $allFilters = [
            'initie' => false,
            'attente_modification' => false,
            'modifier' => false,
            'attente_fond' => false,
            'attente_versement' => false,
            'attente_refuser' => false,
            'debloquer' => false,
            'refuser' => false,
            'reverser' => false,
            'comptabiliser' => false,
            'justifier' => false
        ];

        // Activation des filtres selon l'URL
        if ($requestURI == "/commisaire/") {
            $allFilters = array_fill_keys(array_keys($allFilters), true);
        } else {
            foreach ($allFilters as $filter => &$active) {
                if (str_contains($requestURI, "value=$filter") || str_contains($requestURI, $filter)) {
                    $active = true;
                }
            }
        }

        // Récupération de l'exercice valide et du code de demande
        $exercice_valide = $exerciceRepository->getExerciceValide();
        $code_demande = $demandeRepository->findDemandeByCode(10);
        if (!$exercice_valide) {
            return new Response("Veuillez choisir l'exercice à verifier");
        } else if (!$code_demande) {
            return new Response("Code de demande non valide, verifier le code dans la base de donné");
        }

        // Récupération des demandes de décaissement de fonds selon les filtres actifs
        $demande_types = $demandeTypeRepository->findActiveByExercice($exercice_valide, $allFilters, $code_demande);
        return $this->render('commisaire_compte/index.html.twig', [
            'exercice' => $exercice_valide,
            'demande_types' => $demande_types,
            'filters' => $allFilters,
        ]);
    }

    /**
     * Affiche la liste des approvisionnements pour l'exercice actuellement ouvert/actif.
     *
     * @param Request $request La requête HTTP
     * @param ExerciceRepository $exerciceRepository Le repository pour récupérer l'exercice valide
     * @param DemandeTypeRepository $demandeTypeRepository Le repository pour récupérer les types de demandes
     * @param DemandeRepository $demandeRepository Le repository pour récupérer les demandes
     *
     * @return Response La réponse contenant la vue de la liste des approvisionnements
     */
    #[Route('/approvisionnement', name: 'app_commisaire_approvisionnement')]
    public function list_approvisionnement(Request               $request,
                                           ExerciceRepository    $exerciceRepository,
                                           DemandeTypeRepository $demandeTypeRepository,
                                           DemandeRepository     $demandeRepository): Response
    {
        $exercice_valide = $exerciceRepository->getExerciceValide();
        $code_demande = $demandeRepository->findDemandeByCode(20);
        if (!$exercice_valide) {
            return new Response("Veuillez choisir l'exercice à verifier");
        } else if (!$code_demande) {
            return new Response("Code de demande non valide, verifier le code dans la base de donné");
        }

        // Récupération des demandes d'approvisionnement
        $demande_types = $demandeTypeRepository->findByExerciceAndCode($exercice_valide, $code_demande);
        return $this->render('commisaire_compte/approvisionnement.html.twig', [
            'exercice' => $exercice_valide,
            'liste_approvisio' => $demande_types,
        ]);
    }

    /**
     * Affiche les détails d'un approvisionnement spécifique.
     *
     * @param int $id L'ID du type de demande
     * @param DemandeTypeRepository $demandeTypeRepository Le repository pour récupérer les types de demandes
     * @param ApprovisionnementPieceRepository $approvisionnementPieceRepository Le repository pour récupérer les pièces d'approvisionnement
     * @param ObservationDemandeRepository $observationDemandeRepository Le repository pour récupérer les observations des demandes
     * @param LogDemandeTypeRepository $logDemandeTypeRepository Le repository pour récupérer l'historique des demandes
     *
     * @return Response La réponse contenant la vue avec les détails de l'approvisionnement
     */
    #[Route('/approvisionnement/{id}', name: 'app_commisaire_show_approvisionnement')]
    public function showApprovisionnement($id,
                                          DemandeTypeRepository $demandeTypeRepository,
                                          ApprovisionnementPieceRepository $approvisionnementPieceRepository,
                                          ObservationDemandeRepository $observationDemandeRepository,
                                          LogDemandeTypeRepository $logDemandeTypeRepository): Response
    {
        $demande_type = $demandeTypeRepository->find($id);
        $list_img = $approvisionnementPieceRepository->findByRef($demande_type->getRefDemande());
        $observations = $observationDemandeRepository->findByRefdemande($demande_type->getRefdemande());
        $historique = $logDemandeTypeRepository->findByDemandeType($demande_type);
        return $this->render('commisaire_compte/show_approvisionnement.html.twig', [
            'demande_type' => $demande_type,
            'images' => $list_img,
            'observations' => $observations,
            'historiques' => $historique,
        ]);
    }

    /**
     * Affiche les détails d'une demande de décaissement de fonds spécifique.
     *
     * @param int $id L'ID du type de demande
     * @param DemandeTypeRepository $demandeTypeRepository Le repository pour récupérer les types de demandes
     * @param DetailDemandePieceRepository $detailDemandePieceRepository Le repository pour récupérer les pièces détaillées des demandes
     * @param ObservationDemandeRepository $observationDemandeRepository Le repository pour récupérer les observations des demandes
     * @param LogDemandeTypeRepository $logDemandeTypeRepository Le repository pour récupérer l'historique des demandes
     *
     * @return Response La réponse contenant la vue avec les détails de la demande
     */
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
