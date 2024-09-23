<?php

namespace App\Controller;

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
    public function livre_journal(): Response
    {
        return $this->render('journal/journal_caisse.html.twig');
    }
}
