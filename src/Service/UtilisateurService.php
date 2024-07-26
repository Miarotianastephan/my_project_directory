<?php

namespace App\Service;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use phpDocumentor\Reflection\Types\Boolean;

class UtilisateurService
{

    public $utilisateurRepository;

    public function __construct(UtilisateurRepository $utilisateurRepo) {
        $this->utilisateurRepository = $utilisateurRepo;
    }
 
    public function isExistUser(string $userMatricule, string $userPassword){
        $user = $this->utilisateurRepository->findOneBy(['user_matricule' => $userMatricule]);
        if (!$user->getId()){
            return false;
        }
        return true;
    }
}