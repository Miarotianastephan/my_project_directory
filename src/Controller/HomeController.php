<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use App\Service\UtilisateurService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    
    #[Route(path: '/login', name: 'user_login', methods: ['POST'])]
    public function loginUser(UtilisateurService $userService): Response{

        $user_matricule = "12345";
        $is_exist_user = $userService->isExistUser($user_matricule);
        return new Response(
            '<html><body>Utilisateur status: '.var_dump($is_exist_user).'</body></html>'
        );
    }

}
