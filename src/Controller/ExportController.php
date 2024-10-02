<?php

namespace App\Controller;

use App\Repository\CompteMereRepository;
use App\Repository\MouvementRepository;
use App\Service\EtatFinancierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/export')]
class ExportController extends AbstractController
{
    private $mvtRepository;
    
    public function __construct(MouvementRepository $mvtRepo)
    {
        $this->mvtRepository = $mvtRepo;        
    }
    

    #[Route('/journal', name: 'export.journal')]
    public function index(): Response
    {
        // $journal_caisse = $this->mvtRepository->findAllOrderedByEventDateAndId();
        $journal_caisse = $this->mvtRepository->findAllMouvementById();
        return $this->render('export/index.html.twig', [
            'journal_caisse' => $journal_caisse,
        ]);
    }

    #[Route(path: '/etat/financier', name: 'export.etat', methods: ['GET'])]
    public function etat_fi(EtatFinancierService $etatService, CompteMereRepository $mereRepository){
        $comptesMere = $mereRepository->findAll();
        $etatMontant = $this->mvtRepository->getTotalMouvementGroupedByCompteMere();
        $dataEtatComplet = $etatService->findMontantByCompteMere($etatMontant, $comptesMere, $mereRepository);
        return $this->render('export/etat_financier.html.twig',[
            
        ]);
    }
}
