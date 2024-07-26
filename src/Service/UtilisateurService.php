<?php

namespace App\Service;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManager;
use phpDocumentor\Reflection\Types\Boolean;

class UtilisateurService
{

    public $utilisateurRepository;

    public function __construct(UtilisateurRepository $utilisateurRepo) {
        $this->utilisateurRepository = $utilisateurRepo;
    }
 
    public function isExistUser(string $userMatricule){
        $user = $this->utilisateurRepository->findOneByUserMatricule($userMatricule);
        if(isset($user)){return true;}
        return false;
    }

}