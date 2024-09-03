<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comptable')]
class ComptableController extends AbstractController
{
    #[Route('/', name: 'comptable.graphe', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('comptable/graphe.html.twig');
    }

    #[Route('/form/depense', name: 'comptable.form_depense_directe', methods: ['GET'])]
    public function form_depense_directe(): Response
    {
        return $this->render('comptable/ajout_dep_direct.html.twig');
    }
}