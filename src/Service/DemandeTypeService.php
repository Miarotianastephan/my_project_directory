<?php

namespace App\Service;

use App\Entity\DemandeType;
use App\Repository\DemandeRepository;
use App\Repository\DemandeTypeRepository;
use App\Repository\EtatDemandeRepository;
use App\Repository\PlanCompteRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\SecurityBundle\Security;

class DemandeTypeService
{

    public $demandeTypeRepository;
    public $demandeRepository;
    public $planCompteRepo;
    private $user;
    private $etatDmRepo;

    public function __construct(PlanCompteRepository $plan_compte_repo,DemandeRepository $demande_repo,DemandeTypeRepository $dm_typeRepo, EtatDemandeRepository $etatDemandeRepo , Security $security) {
        $this->demandeTypeRepository = $dm_typeRepo;
        $this->user = $security->getUser(); 
        $this->demandeRepository = $demande_repo;
        $this->planCompteRepo = $plan_compte_repo;
        $this->etatDmRepo = $etatDemandeRepo;
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
    
    public function insertDemandeType($exercice, $planCptEntityId, $planCptMotifId, $montantDemande, $modePaiement, $dateSaisie, $dateOperation){
        // createReferenceDemande : gérer dans la base de donnée par un trigger 
        // createTypeDeDemande : toujours de type demande de décaissement
        $demande = $this->demandeRepository->findDemandeByCode(10);//Code demande 10 => Décaissement

        $demande_type = new DemandeType();
        $demande_type->setDmMontant($montantDemande);

        
        $entity_code = $this->planCompteRepo->find($planCptEntityId);       // getPlanCompte ENTITE by ID
        $plan_compte_motif = $this->planCompteRepo->find($planCptMotifId);  // getPlanCompteMotif by ID

        $demande_type->setEntityCode($entity_code);
        $demande_type->setDmModePaiement($modePaiement);
        $demande_type->setDmEtat( $this->etatDmRepo, 100 );                 // 100 Initié OK_ETAT
        $demande_type->setUtilisateur($this->user);
        $demande_type->setPlanCompte($plan_compte_motif);
        $demande_type->setExercice($exercice);
        $demande_type->setDemande($demande);
        $demande_type->setDmDate(new \DateTime($dateSaisie));
        $demande_type->setDmDateOperation(new \DateTime($dateOperation));

        $response_data = $this->demandeTypeRepository->insertDemandeType($demande_type);
        return $response_data;
    }

    public function findAllMyDemandeTypes(){
        $data = $this->demandeTypeRepository->findByUtilisateur($this->user);
        foreach ($data as $key => $value) {
            # code...
        }
        return $data;
    }

}