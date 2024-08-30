<?php

namespace App\Service;

use App\Repository\GroupeUtilisateurRepository;

class GroupeUtilisateurService
{

    public $groupeUtilisateurRepository;

    public function __construct(GroupeUtilisateurRepository $groupeUtilisateurRepo) {
        $this->groupeUtilisateurRepository = $groupeUtilisateurRepo;
    }

    public function convertIdGroupeToRole($idGroupe, $isToArray){
        $groupe = $this->groupeUtilisateurRepository->find($idGroupe);
        $groupeNiveau = $groupe->getGrpNiveau();
        $role = [];
        array_push($role, 'ROLE_'.$groupeNiveau);
        if($isToArray == false){
            return json_encode($role);
        }return $role;
    }

}