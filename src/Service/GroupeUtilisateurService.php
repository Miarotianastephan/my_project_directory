<?php

namespace App\Service;

use App\Repository\GroupeUtilisateurRepository;

/**
 * Service de gestion des groupes d'utilisateurs.
 *
 * Cette classe permet de gérer les groupes d'utilisateurs et leurs rôles associés. Elle offre une fonctionnalité pour
 * convertir un identifiant de groupe en un rôle correspondant. Le rôle est construit à partir du niveau du groupe
 * et peut être renvoyé sous forme de tableau ou de chaîne JSON.
 */
class GroupeUtilisateurService
{

    public $groupeUtilisateurRepository;

    public function __construct(GroupeUtilisateurRepository $groupeUtilisateurRepo)
    {
        $this->groupeUtilisateurRepository = $groupeUtilisateurRepo;
    }

    /**
     * Convertit l'identifiant d'un groupe en un rôle sous forme de tableau ou de chaîne JSON.
     *
     * Cette méthode utilise le niveau du groupe pour construire le rôle associé. Le rôle est formaté en ajoutant le préfixe
     * `ROLE_` suivi du niveau du groupe. Elle permet de retourner le rôle sous forme de tableau ou de chaîne JSON en fonction
     * du paramètre `isToArray`.
     *
     * @param int $idGroupe L'identifiant du groupe utilisateur dont on veut obtenir le rôle.
     * @param bool $isToArray Indique si le résultat doit être retourné sous forme de tableau (true) ou de chaîne JSON (false).
     * @return array|string Le rôle correspondant au groupe, soit sous forme de tableau, soit de chaîne JSON.
     */
    public function convertIdGroupeToRole($idGroupe, $isToArray)
    {
        // Recherche du groupe utilisateur dans le repository
        $groupe = $this->groupeUtilisateurRepository->find($idGroupe);

        // Obtention du niveau du groupe
        $groupeNiveau = $groupe->getGrpNiveau();

        // Construction du rôle à partir du niveau du groupe
        $role = [];
        array_push($role, 'ROLE_' . $groupeNiveau);

        // Retourne le rôle sous forme de chaîne JSON ou de tableau en fonction du paramètre $isToArray
        if ($isToArray == false) {
            return json_encode($role);
        }
        return $role;
    }

}