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

    public function uploadImage($file , string $destination) :string
    {
        try {
            // Obtenir le nom de fichier original
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            // Générer un nom unique pour éviter les conflits
            $newFilename = uniqid() . '.' . $file->guessExtension();


            // Déplacer le fichier dans le répertoire de destination
            $file->move($destination, $newFilename);
            return $newFilename;
        }catch (\Exception $e){
            throw new ('Erreur lors du téléchargement du fichier : ' . $e->getMessage());
        }
    }

}