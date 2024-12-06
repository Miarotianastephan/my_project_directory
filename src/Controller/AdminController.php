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

/**
 * Contrôleur dédié à la gestion des utilisateurs pour la page ADMIN.
 *
 * Ce contrôleur permet la gestion des utilisateurs (ajout, modification, suppression) et l'affichage de la liste des utilisateurs.
 * Il fournit aussi des routes pour gérer les groupes d'utilisateurs et la conversion des groupes en rôles.
 */

#[Route('/admin')]
class AdminController extends AbstractController
{

    /**
     * Page de liste de tous les utilisateurs pour la page ADMIN.
     *
     * Cette fonction permet d'afficher tous les utilisateurs dans l'interface admin.
     *
     * @param UtilisateurRepository $utilisateurRepository Le repository pour accéder aux données des utilisateurs.
     * @return \Symfony\Component\HttpFoundation\Response La réponse qui rend le template avec la liste des utilisateurs.
     */
    #[Route(path: '/utilisateurs', name: 'admin_users', methods: ['GET'])]
    public function listUtilisateursAdmin(UtilisateurRepository $utilisateurRepository)
    {
        return $this->render('back_office/admin_list_utilisateur.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll()
        ]);
    }

    /**
     * Formulaire d'ajout d'utilisateur pour la page ADMIN.
     *
     * Cette fonction permet de rendre un formulaire pour ajouter un nouvel utilisateur, en affichant les groupes disponibles.
     *
     * @param GroupeUtilisateurRepository $groupeRepository Le repository pour accéder aux données des groupes d'utilisateurs.
     * @return \Symfony\Component\HttpFoundation\Response La réponse qui rend le formulaire d'ajout.
     */
    #[Route(path: '/utilisateurs/ajout', name: 'admin_add_user', methods: ['GET'])]
    public function formAddUtilisateur(GroupeUtilisateurRepository $groupeRepository)
    {
        return $this->render('back_office/admin_add_user.html.twig', [
            'groupes' => $groupeRepository->findAll()
        ]);
    }

    /**
     * Sauvegarde d'un nouvel utilisateur.
     *
     * Cette fonction enregistre un nouvel utilisateur dans la base de données. Le matricule de l'utilisateur, l'ID du groupe,
     * et le rôle sont récupérés et traités. Si l'utilisateur existe déjà, un message d'erreur est retourné.
     *
     * @param Request $request La requête HTTP contenant les données de l'utilisateur à ajouter.
     * @param UtilisateurRepository $utilisateurRepository Le repository pour accéder aux données des utilisateurs.
     * @param UtilisateurService $userService Le service permettant de vérifier l'existence de l'utilisateur.
     * @param GroupeUtilisateurService $groupeService Le service permettant de convertir l'ID du groupe en rôle.
     * @return JsonResponse La réponse JSON indiquant le succès ou l'échec de l'ajout.
     */
    #[Route(path: '/utilisateurs/save/test', name: 'admin_save_user_test', methods: ['POST'])]
    public function saveUtilisateurTest(Request $request, UtilisateurRepository $utilisateurRepository, UtilisateurService $userService, GroupeUtilisateurService $groupeService)
    {
        $data = json_decode($request->getContent(), true);

        $request_status = [];
        $user_matricule = $data["user_matricule"];
        $id_groupe = $data["id_groupe"];

        // Vérification si l'utilisateur existe déjà
        $is_active_user = $userService->isExistUser($user_matricule);
        if ($is_active_user['isExist'] == false) {
            // Insertion de l'utilisateur
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
     * Modification d'un utilisateur.
     *
     * Cette fonction permet de modifier un utilisateur existant, y compris son groupe et ses rôles.
     *
     * @param Request $request La requête HTTP contenant les données de l'utilisateur à modifier.
     * @param UtilisateurRepository $utilisateurRepo Le repository pour accéder aux données des utilisateurs.
     * @param GroupeUtilisateurRepository $groupeRepo Le repository pour accéder aux groupes d'utilisateurs.
     * @param GroupeUtilisateurService $groupeService Le service permettant de convertir l'ID du groupe en rôle.
     * @return JsonResponse La réponse JSON contenant les données mises à jour de l'utilisateur.
     */
    #[Route(path: '/utilisateurs/edit', name: 'admin_edit_user', methods: ['POST'])]
    public function updateUtilisateur(Request $request, UtilisateurRepository $utilisateurRepo, GroupeUtilisateurRepository $groupeRepo, GroupeUtilisateurService $groupeService)
    {
        //dump('PRINTING REQUSET/RESPONSE');
        // Récupère le groupe à affecter
        $grp_id_update = $request->request->get('usr_grp_id');
        $groupe_updated = $groupeRepo->find($grp_id_update);
        $role_updated = $groupeService->convertIdGroupeToRole($grp_id_update, true);

        // Récupère l'utilisateur à modifier
        $user_id = $request->request->get('usr_id');
        $user_to_update = $utilisateurRepo->find($user_id);
        $user_to_update->setGroupUtilisateur($groupe_updated);
        $user_to_update->setRoles($role_updated);
        $response_data = $utilisateurRepo->updateUtilisateur($user_to_update);

        return new JsonResponse($response_data);
    }

    /**
     * Suppression d'un utilisateur.
     *
     * Cette fonction permet de supprimer un utilisateur. Les données de l'utilisateur à supprimer sont récupérées depuis la requête.
     *
     * @param Request $request La requête HTTP contenant les données de l'utilisateur à supprimer.
     * @param UtilisateurRepository $utilisateurRepository Le repository pour accéder aux données des utilisateurs.
     * @return JsonResponse La réponse JSON indiquant les détails de l'utilisateur supprimé.
     */
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
     * Fonction pour obtenir la liste des groupes sans ceux déjà attribués.
     *
     * Cette fonction permet de récupérer tous les groupes qui ne sont pas encore assignés à un utilisateur spécifique.
     *
     * @param Request $request La requête HTTP contenant l'ID du groupe actuel de l'utilisateur.
     * @param GroupeUtilisateurRepository $groupeRepository Le repository pour accéder aux groupes d'utilisateurs.
     * @param SerializerInterface $serializer Le service de sérialisation pour convertir les objets en JSON.
     * @return JsonResponse La réponse JSON contenant les groupes non attribués.
     */
    #[Route(path: '/utilisateurs/find/groups', name: 'admin_group_user', methods: ['POST'])]
    public function getAllGroupsToJson(Request $request, GroupeUtilisateurRepository $groupeRepository, SerializerInterface $serializer)
    {
        $data = json_decode($request->getContent(), true);
        $group_id_actuel = $data['usr_grp_id'];
        $all_group_except = $groupeRepository->findGroupsNotAssignedToUser($group_id_actuel);
        // Sérialiser les objets en JSON
        $jsonContent = $serializer->serialize($all_group_except, 'json');
        // Retourner une réponse JSON
        return new JsonResponse($jsonContent, 200, [], true);
    }

}
