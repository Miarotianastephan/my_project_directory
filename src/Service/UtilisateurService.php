<?php

namespace App\Service;

use App\Repository\UtilisateurRepository;

class UtilisateurService
{

    public $utilisateurRepository;

    public function __construct(UtilisateurRepository $utilisateurRepo)
    {
        $this->utilisateurRepository = $utilisateurRepo;
    }

    /**
     * Vérifie si un utilisateur existe dans la base de données en fonction de son matricule.
     *
     * Cette méthode interroge le repository des utilisateurs pour vérifier si un utilisateur
     * avec le matricule fourni existe. Elle retourne un tableau indiquant si l'utilisateur
     * existe ainsi que ses données s'il est trouvé.
     *
     * @param string $userMatricule Le matricule de l'utilisateur à vérifier.
     *
     * @return array Un tableau contenant un indicateur d'existence (`isExist`) et les données de l'utilisateur
     *               si trouvé (`dataUser`), ou `null` si l'utilisateur n'existe pas.
     */
    public function isExistUser(string $userMatricule)
    {
        $user = $this->utilisateurRepository->findOneByUserMatricule($userMatricule);
        if ($user !== null) {
            // L'utilisateur existe, retourne les données de l'utilisateur
            $log_status = ['isExist' => true, 'dataUser' => $user];
            return $log_status;
        }
        // L'utilisateur n'existe pas, retourne un tableau avec un statut false
        $log_status = ['isExist' => false, 'dataUser' => null];
        return $log_status;
    }

}