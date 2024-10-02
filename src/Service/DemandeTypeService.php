<?php

namespace App\Service;

use App\Entity\DemandeType;
use App\Entity\Utilisateur;
use App\Repository\DemandeRepository;
use App\Repository\DemandeTypeRepository;
use App\Repository\EtatDemandeRepository;
use App\Repository\LogDemandeTypeRepository;
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
    private $logDmRepository;

    public function __construct(
        PlanCompteRepository $plan_compte_repo,
        DemandeRepository $demande_repo,
        DemandeTypeRepository $dm_typeRepo, 
        EtatDemandeRepository $etatDemandeRepo , 
        Security $security,
        LogDemandeTypeRepository $logDemandeType) 
    {
        $this->demandeTypeRepository = $dm_typeRepo;
        $this->user = $security->getUser(); 
        $this->demandeRepository = $demande_repo;
        $this->planCompteRepo = $plan_compte_repo;
        $this->etatDmRepo = $etatDemandeRepo;
        $this->logDmRepository = $logDemandeType;
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
        $demande = $this->demandeRepository->findDemandeByCode(10);         // Code demande 10 => Decaissement 

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

    public function findAllMyDemandeTypesInit(){
        $data = $this->demandeTypeRepository->findByUtilisateur($this->user);
        return $data;
    }

    public function findAllMyDemandeWithLog(){
        $temp_user = new Utilisateur($this->user);
        $user_matricule = $temp_user->getUserMatricule();
        $logs_demande = $this->logDmRepository->findLogsForUserMatricule($user_matricule);
        return $logs_demande;
    }

    public function findAllMyDemande(){
        $all_demande = $this->demandeTypeRepository->findAll(); // toute les demandes
        $user_matricule = $this->user->getUserMatricule();
        $my_demande = [];

        foreach ($all_demande as $dm) {
            if($dm->getUtilisateur()->getUserMatricule() == $user_matricule){   // si anazy ilay demandes
                array_push($my_demande, $dm);
            }
            else{                                                               //si tsia dia asesy ny log 
                $dm_logs = $dm->getLogDemandeTypes();
                foreach ($dm_logs as $log) {
                    if($log->getUserMatricule() == $user_matricule){
                        array_push($my_demande, $dm);
                        // return;
                    }
                }
            }
        }
        return $my_demande;
    }

}