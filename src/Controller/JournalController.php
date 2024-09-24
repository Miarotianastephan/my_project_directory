<?php

namespace App\Controller;

use App\Entity\CompteMere;
use App\Entity\Evenement;
use App\Entity\Exercice;
use App\Entity\Mouvement;
use App\Entity\PlanCompte;
use App\Entity\TransactionType;
use App\Entity\Utilisateur;
use App\Repository\MouvementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function livre_journal(MouvementRepository $mouvementRepository): Response
    {
        $list_evenement = $mouvementRepository->findAll();
        return $this->render('journal/journal_caisse.html.twig', ['mouvements' => $list_evenement]);
    }
}
