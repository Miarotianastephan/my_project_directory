<?php

namespace App\Service;

use App\Repository\DemandeTypeRepository;
use App\Repository\UtilisateurRepository;

class DemandeTypeService
{

    public $demandeTypeRepository;

    public function __construct(DemandeTypeRepository $dm_typeRepo) {
        $this->demandeTypeRepository = $dm_typeRepo;
    }


}