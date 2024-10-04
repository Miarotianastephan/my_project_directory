<?php

namespace App\Service;

use App\Entity\DemandeType;
use App\Entity\Evenement;
use App\Entity\Mouvement;
use App\Entity\Utilisateur;
use App\Repository\DemandeRepository;
use App\Repository\DemandeTypeRepository;
use App\Repository\DetailTransactionCompteRepository;
use App\Repository\EtatDemandeRepository;
use App\Repository\ExerciceRepository;
use App\Repository\LogDemandeTypeRepository;
use App\Repository\PlanCompteRepository;
use App\Repository\TransactionTypeRepository;
use App\Repository\UtilisateurRepository;
use DateTime;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class DemandeTypeService
{

    public $demandeTypeRepository;
    public $demandeRepository;
    public $planCompteRepo;
    private $user;
    private $etatDmRepo;
    private $logDmRepository;
    private $exercicerepository;
    private $trsTypeRepo;
    private $detailTrsRepo;

    public function __construct(
        PlanCompteRepository $plan_compte_repo,
        DemandeRepository $demande_repo,
        DemandeTypeRepository $dm_typeRepo, 
        EtatDemandeRepository $etatDemandeRepo , 
        Security $security,
        ExerciceRepository $exercicerepository,
        LogDemandeTypeRepository $logDemandeType, 
        TransactionTypeRepository $trsTypeRepo, 
        DetailTransactionCompteRepository $detailTrsRepo) 
    {
        $this->demandeTypeRepository = $dm_typeRepo;
        $this->user = $security->getUser(); 
        $this->demandeRepository = $demande_repo;
        $this->planCompteRepo = $plan_compte_repo;
        $this->etatDmRepo = $etatDemandeRepo;
        $this->logDmRepository = $logDemandeType;
        $this->exercicerepository = $exercicerepository;
        $this->trsTypeRepo = $trsTypeRepo;
        $this->detailTrsRepo = $detailTrsRepo;
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

    // Insertion d'approvisionnement
    public function insertDemandeTypeAppro($exercice, $planCptEntityId, $montantDemande, $modePaiement, $dateSaisie, $dateOperation, int $tresorier_user_id){
        // createReferenceDemande : gérer dans la base de donnée par un trigger 
        // createTypeDeDemande : toujours de type demande de décaissement
        $demande = $this->demandeRepository->findDemandeByCode(20);         // Code demande 20 => Approvisionnement 

        $demande_type = new DemandeType();
        $demande_type->setDmMontant($montantDemande);

        
        $entity_code_and_plan_compte_motif = $this->planCompteRepo->find($planCptEntityId);       // getPlanCompte ENTITE by ID

        $demande_type->setEntityCode($entity_code_and_plan_compte_motif);
        $demande_type->setDmModePaiement($modePaiement);
        $demande_type->setDmEtat( $this->etatDmRepo, 500 );                 // 500 Comptabilisation OK_ETAT
        $demande_type->setUtilisateur($this->user);
        $demande_type->setPlanCompte($entity_code_and_plan_compte_motif);
        $demande_type->setExercice($exercice);
        $demande_type->setDemande($demande);
        $demande_type->setDmDate(new \DateTime($dateSaisie));
        $demande_type->setDmDateOperation(new \DateTime($dateOperation));

        $response_data = $this->demandeTypeRepository->insertDemandeType($demande_type);
        dump($response_data);
        
        // Find last insert approvisionnement
        $demande_type = $this->demandeTypeRepository->findLastInsertedDemandeType();
        dump($demande_type);
        
        $entityManager = $this->demandeTypeRepository->getEntityManager();
        $entityManager->beginTransaction();
        try {
        // Comptabilisation 
        $user_tresorier = $entityManager->find(Utilisateur::class, $tresorier_user_id);
            // les données à utiliser
            $reference_demande = $demande_type->getRefDemande();
            $exercice_demande = $this->exercicerepository->getExerciceValide();
            $montant_demande = $demande_type->getDmMontant();
            $numero_compte_debit = $demande_type->getPlanCompte();
            // $mode_paiement_demande = (int)($demande_type->getDmModePaiement());
            $transaction_a_faire = $this->trsTypeRepo->findTransactionForApprovision();   // identifier le type de transaction à faire
            $detail_transaction = $this->detailTrsRepo->findByTransactionWithTypeOperation($transaction_a_faire, 0);                // identifier le mouvement à créditer
            // dump($detail_transaction);
            // Création evenement 
            $evenement = new Evenement();
            $evenement->setEvnTrsId($transaction_a_faire);
            $evenement->setEvnResponsable($user_tresorier);
            $evenement->setEvnExercice($exercice_demande);
            $evenement->setEvnCodeEntity($demande_type->getEntityCode()->getCptLibelle());
            $evenement->setEvnMontant($montant_demande);
            $evenement->setEvnReference($reference_demande);
            $evenement->setEvnDateOperation(new DateTime());
            $entityManager->persist($evenement);
            // Création des mouvements
            
            $mv_debit = new Mouvement();                        // DEBIT
            $mv_debit->setMvtEvenementId($evenement);
            $mv_debit->setMvtMontant($montant_demande);
            $mv_debit->setMvtDebit(true);
            $mv_debit->setMvtCompteId($numero_compte_debit);
            $entityManager->persist($mv_debit);
            
            $mv_credit = new Mouvement();                       // CREDIT
            $mv_credit->setMvtEvenementId($evenement);
            $mv_credit->setMvtMontant($montant_demande);
            $mv_credit->setMvtDebit(false);
            $mv_credit->setMvtCompteId($detail_transaction->getPlanCompte());
            $entityManager->persist($mv_credit);

            $entityManager->flush();
            $entityManager->commit();                           // si tout OK 
        } catch (\Throwable $th) {
            $entityManager->rollback();                         // si erreur opération 
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur transaction : ' . $th->getMessage()
            ]);
        }
        return $response_data;
    }

}