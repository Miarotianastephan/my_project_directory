<?php

namespace App\Controller;

use App\Repository\CompteMereRepository;
use App\Repository\ExerciceRepository;
use App\Repository\MouvementRepository;
use App\Service\EtatFinancierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/export')]
class ExportController extends AbstractController
{
    private $mvtRepository;
    private $exoRepository;
    
    public function __construct(MouvementRepository $mvtRepo, ExerciceRepository $exoRepo)
    {
        $this->mvtRepository = $mvtRepo;
        $this->exoRepository = $exoRepo;        
    }
    

    #[Route('/journal', name: 'export.journal')]
    public function index(): Response
    {
        // $journal_caisse = $this->mvtRepository->findAllOrderedByEventDateAndId();
        $journal_caisse = $this->mvtRepository->findAllMouvementById();
        return $this->render('export/index.html.twig', [
            'journal_caisse' => $journal_caisse,
            'exercice_actuel' => $this->exoRepository->getExerciceValide()->getExerciceDateDebut()->format('Y')
        ]);
    }

    #[Route(path: '/etat/financier', name: 'export.etat', methods: ['GET'])]
    public function etat_fi(EtatFinancierService $etatService, CompteMereRepository $mereRepository){
        $comptesMere = $mereRepository->findAll();
        // $etatMontant = $this->mvtRepository->getTotalMouvementGroupedByPlanCompte();
        $etatMontant = $this->mvtRepository->getSoldeRestantByMouvement();
        $dataEtatComplet = $etatService->findMontantByCompteMere2($etatMontant, $comptesMere, $mereRepository);
        return $this->render('export/etat_financier.html.twig',[
            'etatComplet' => $dataEtatComplet,
        ]);
    }
    #[Route('/journal/search', name: 'export.journal.search', methods: ['POST'])]
    public function searchInJournal(Request $request)
    {
        // $dataHttpForm = json_decode($request->getPara(), true);
        $request_status = ['message' => 'TEST', 'status' => true] ;
        $rech_numero = (strlen(trim($request->get('rech_numero')))>0) ? $request->get('rech_numero') : null;
        $rech_libelle = (strlen(trim($request->get('rech_libelle')))>0) ? $request->get('rech_libelle') : null;
        $date_inf = $request->get('date_inf');
        $date_sup = $request->get('date_sup');
        // Appel du fonction pour la recherche
        $search_result = $this->mvtRepository->searchDataMouvement($rech_numero,$rech_libelle,$date_inf,$date_sup);
        $request_status['search_result'] = $search_result;
        return new JsonResponse($request_status);
    }
}
