<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DemandeurController extends AbstractController
{
    #[Route('/demandeur', name: 'nouveau_demande')]
    public function index(): Response
    {
        return $this->render('demandeur/demandeur.html.twig');
    }
}
