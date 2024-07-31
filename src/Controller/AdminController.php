<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use App\Service\UtilisateurService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AdminController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('back_office/index.html.twig');
    }
    
    #[Route(path: '/login', name: 'user_login', methods: ['POST'])]
    public function loginUser(UtilisateurService $userService, Request $request, SerializerInterface $json_serial): Response{

        $data = json_decode($request->getContent(), true);
        $user_matricule = $data["user_matricule"];
        $user_pass = $data["user_pass"];

        $user_status = $userService->isExistUser($user_matricule);
        $is_exist_user = $user_status['isExist'];
        $url_admin_index = $this->generateUrl('admin_index');
        $login_status = ($is_exist_user==true) ? 
        ['message' => 'Reponse', 'valeur' => $is_exist_user, 'path' => $url_admin_index] :
        ['message' => 'Reponse', 'valeur' => $is_exist_user] ;
        // $login_status = $json_serial->serialize($login_status, 'json');

        // $session = $request->getSession();
        // if ($is_exist_user==true){
        //     $session->set('user', $user_status[]);
        // }

        // return JsonResponse::fromJsonString($login_status);
        return new JsonResponse($login_status);
    }

    #[Route(path: '/admin/index', name: 'admin_index', methods: ['GET'])]
    public function acceuilAdmin(){
        return $this->render('back_office/admin_index.html.twig',[
            
        ]);  
    }

    #[Route(path: '/logout', name: 'user_logout', methods: ['GET'])]
    public function logoutUser(){
        return $this->redirectToRoute('app_home');
    }

}