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
        // dump($user);
        if($user !== null){
            $log_status = ['isExist' => true,'dataUser' => $user];
            // dump($log_status);
            return $log_status;
        }
        $log_status = ['isExist' => false,'dataUser' => null];
        // dump($log_status);
        return $log_status;
    }

}