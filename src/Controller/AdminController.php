<?php

namespace App\Controller;

use App\Repository\GroupeUtilisateurRepository;
use App\Repository\UtilisateurRepository;
use App\Service\GroupeUtilisateurService;
use App\Service\UtilisateurService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/admin')]
class AdminController extends AbstractController
{
    /**
     * Page de liste de tous les utilisateurs pour la page ADMIN.
     * */
    #[Route(path: '/utilisateurs', name: 'admin_users', methods: ['GET'])]
    public function listUtilisateursAdmin(UtilisateurRepository $utilisateurRepository)
    {
        return $this->render('back_office/admin_list_utilisateur.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll()
        ]);
    }

    /**
     * Formulaire d'ajout d'utilisateur pour la page ADMIN
     **/
    #[Route(path: '/utilisateurs/ajout', name: 'admin_add_user', methods: ['GET'])]
    public function formAddUtilisateur(GroupeUtilisateurRepository $groupeRepository)
    {
        return $this->render('back_office/admin_add_user.html.twig', [
            'groupes' => $groupeRepository->findAll()
        ]);
    }

    /**
     * Sauvegarde d'un nouvel utilisateur (string $user_matricule, int $groupe_id, string $role)
     * * Le role à stocker est en format ['ROLE_0'], ['ROLE_10'], ... => à récuperer à partir de convertIdGroupeToRole()
     **/
    #[Route(path: '/utilisateurs/save/test', name: 'admin_save_user_test', methods: ['POST'])]
    public function saveUtilisateurTest(Request $request, UtilisateurRepository $utilisateurRepository, UtilisateurService $userService, GroupeUtilisateurService $groupeService)
    {
        $data = json_decode($request->getContent(), true);

        $request_status = [];
        $user_matricule = $data["user_matricule"];
        $id_groupe = $data["id_groupe"];

        // verification 
        $is_active_user = $userService->isExistUser($user_matricule);
        if ($is_active_user['isExist'] == false) {
            // insertion
            $role = $groupeService->convertIdGroupeToRole($id_groupe, false);
            $utilisateurRepository->insertUtilisateur($user_matricule, $id_groupe, $role);
            $message = 'Utilisateur ' . $user_matricule . ' ajouté avec success !';
            $request_status = ['message' => $message, 'status' => true];
        } else {
            $message = 'Utilisateur ' . $user_matricule . ' existe déjà !';
            $request_status = ['message' => $message, 'status' => false];
        }
        return new JsonResponse($request_status);
    }

    /**
     * Modification d'un utilisateur
     **/
    #[Route(path: '/utilisateurs/edit', name: 'admin_edit_user', methods: ['POST'])]
    public function updateUtilisateur(Request $request, UtilisateurRepository $utilisateurRepo, GroupeUtilisateurRepository $groupeRepo, GroupeUtilisateurService $groupeService)
    {
        dump('PRINTING REQUSET/RESPONSE');
        // find the group to assign
        $grp_id_update = $request->request->get('usr_grp_id');
        $groupe_updated = $groupeRepo->find($grp_id_update);
        $role_updated = $groupeService->convertIdGroupeToRole($grp_id_update, true);

        // find the user to update
        $user_id = $request->request->get('usr_id');
        $user_to_update = $utilisateurRepo->find($user_id);
        $user_to_update->setGroupUtilisateur($groupe_updated);
        $user_to_update->setRoles($role_updated);
        $response_data = $utilisateurRepo->updateUtilisateur($user_to_update);

        return new JsonResponse($response_data);
    }

    #[Route(path: '/utilisateurs/delete', name: 'admin_delete_user', methods: ['POST'])]
    public function deleteUtilisateur(Request $request, UtilisateurRepository $utilisateurRepository)
    {
        $data = json_decode($request->getContent(), true);
        $response_data = [
            'usr_id' => $data['usr_id'],
            'usr_matricule' => $data['usr_matricule'],
            'usr_grp_id' => $data['usgrp_id'],
        ];
        return new JsonResponse($response_data);
    }

    /**
     * Fonction pour avoir la liste des groupes sans des spécifiées
     **/
    #[Route(path: '/utilisateurs/find/groups', name: 'admin_group_user', methods: ['POST'])]
    public function getAllGroupsToJson(Request $request, GroupeUtilisateurRepository $groupeRepository, SerializerInterface $serializer)
    {
        $data = json_decode($request->getContent(), true);
        $group_id_actuel = $data['usr_grp_id'];
        $all_group_except = $groupeRepository->findGroupsNotAssignedToUser($group_id_actuel);
        // $all_group_except = $groupeRepository->findAll();
        // Sérialiser les objets en JSON
        $jsonContent = $serializer->serialize($all_group_except, 'json');
        // dump($jsonContent);
        // Retourner une réponse JSON
        return new JsonResponse($jsonContent, 200, [], true);
    }

}
