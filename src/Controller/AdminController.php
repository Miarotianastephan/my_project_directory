<?php

namespace App\Controller;

use App\Entity\GroupeUtilisateur;
use App\Entity\Utilisateur;
use App\Repository\GroupeUtilisateurRepository;
use App\Repository\UtilisateurRepository;
use App\Service\UtilisateurService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\SerializerInterface;

class AdminController extends AbstractController
{

    #[Route(path: '/', name: 'user_login')]
    public function loginUser(Request $request,AuthenticationUtils $authUtils): Response{

        $error = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('back_office/index.html.twig',[
            'error' => $error,
            'lastUsername' => $lastUsername,
        ]);
    }
    #[Route(path: '/logout', name: 'user_logout', methods: ['GET'])]
    public function logoutUser(){}
    
    // #[Route(path: '/login', name: 'user_login', methods: ['POST'])]
    // public function loginUser(UtilisateurService $userService, Request $request, SerializerInterface $json_serial): Response{

    //     $data = json_decode($request->getContent(), true);
    //     $user_matricule = $data["user_matricule"];
    //     $user_pass = $data["user_pass"];
    //     $login_status = [];

    //     // AJOUTER APPEL ACTIVE DIRECTORY
    //     $is_active_user = true;
    //     // APPEL DIRECTORY
    //     if ($is_active_user==true){
    //         $user_status = $userService->isExistUser($user_matricule);
    //         $is_exist_user = $user_status['isExist'];
    //         $url_admin_add_user = $this->generateUrl('admin_add_user');
    //         $login_status = ($is_exist_user==true) ? 
    //         ['message' => 'OK', 'valeur' => $is_exist_user, 'path' => $url_admin_add_user] :
    //         ['message' => 'Erreur', 'valeur' => $is_exist_user] ;
    //     }else{
    //         $login_status = ['message' => 'Erreur', 'valeur' => 'Utilisateur'];
    //     }

    //     // $session = $request->getSession();
    //     // if ($is_exist_user==true){
    //     //     $session->set('user', $user_status[]);
    //     // }
    //     return new JsonResponse($login_status);
    // }

    #[Route(path: '/utilisateurs', name: 'admin_users', methods: ['GET'])]
    public function listUtilisateursAdmin(UtilisateurRepository $utilisateurRepository){
        return $this->render('back_office/admin_list_utilisateur.html.twig',[
            'utilisateurs' => $utilisateurRepository->findAll()
        ]);  
    }

    #[Route(path: '/utilisateurs/ajout', name: 'admin_add_user', methods: ['GET'])]
    public function formAddUtilisateur(GroupeUtilisateurRepository $groupeRepository){
        return $this->render('back_office/admin_add_user.html.twig',[
            'groupes' => $groupeRepository->findAll()
        ]);  
    }
    
    #[Route(path: '/utilisateurs/save/test', name: 'admin_save_user_test', methods: ['POST'])]
    public function saveUtilisateurTest(Request $request, UtilisateurRepository $utilisateurRepository, UtilisateurService $userService){
        $data = json_decode($request->getContent(), true);
        $request_status = [];
        $user_matricule = $data["user_matricule"];
        $id_groupe = $data["id_groupe"];
        // verification 
        $is_active_user = $userService->isExistUser($user_matricule);
        if($is_active_user == false){
            // insertion
            $utilisateurRepository->insertUtilisateur($user_matricule,$id_groupe);
            $message = 'Utilisateur '.$user_matricule.' ajouté avec success !';
            $request_status = ['message' => $message, 'status' => true] ;
        }else{
            $message = 'Utilisateur '.$user_matricule.' existe déjà !';
            $request_status = ['message' => $message, 'status' => false] ;
        }
        return new JsonResponse($request_status);
    }
    

}
