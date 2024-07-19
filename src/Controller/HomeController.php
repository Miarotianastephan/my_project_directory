<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
    public function loginAction(Request $request): Response{
        
        $data = $request->request->get("matricule");
        $all = $request->request->all();


        return $this->render('pages/create_user.html.twig',[
            'login_parameters' => "HELLO WORLD ".$data,
            'all_data' => $all,
        ]);
    }
}
