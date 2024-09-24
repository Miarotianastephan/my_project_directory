<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExceptionController extends AbstractController
{
    #[Route('/error/403', name: 'app_access_denied')]
    public function index(): Response
    {
        return $this->render('exception/error403.html.twig', [
            'controller_name' => 'ExceptionController',
        ]);
    }

    // #[Route('/error/404', name: 'app_access_denied')]
    // public function pageNotFound(): Response
    // {
    //     return $this->render('exception/error403.html.twig', [
    //         'controller_name' => 'ExceptionController',
    //     ]);
    // }
}
