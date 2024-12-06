<?php

namespace App\Service;

use App\Entity\DemandeType;
use App\Entity\Evenement;
use App\Entity\LogDemandeType;
use App\Entity\Mouvement;
use App\Entity\Utilisateur;
use App\Repository\CompteMereRepository;
use App\Repository\DemandeRepository;
use App\Repository\DemandeTypeRepository;
use App\Repository\DetailTransactionCompteRepository;
use App\Repository\EtatDemandeRepository;
use App\Repository\ExerciceRepository;
use App\Repository\LogDemandeTypeRepository;
use App\Repository\PlanCompteRepository;
use App\Repository\TransactionTypeRepository;
use DateTime;
use InvalidArgumentException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class DemandeTypeService
{

    public $cptMereRepo;
    public $demandeTypeRepository;
    public $demandeRepository;
    public $planCompteRepo;
    private $user;
    private $etatDmRepo;
    private $logDmRepository;
    private $exercicerepository;
    private $trsTypeRepo;
    private $detailTrsRepo;

    public function __construct(CompteMereRepository              $compteMereRepo,
                                PlanCompteRepository              $plan_compte_repo,
                                DemandeRepository                 $demande_repo,
                                DemandeTypeRepository             $dm_typeRepo,
                                EtatDemandeRepository             $etatDemandeRepo,
                                Security                          $security,
                                ExerciceRepository                $exercicerepository,
                                LogDemandeTypeRepository          $logDemandeType,
                                TransactionTypeRepository         $trsTypeRepo,
                                DetailTransactionCompteRepository $detailTrsRepo)
    {
        $this->cptMereRepo = $compteMereRepo;
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

    /**
     * Gère le téléchargement d'un fichier image.
     * Génère un nom de fichier unique et déplace le fichier dans le répertoire de destination.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file Le fichier à télécharger.
     * @param string $destination Le répertoire de destination.
     * @return string Le nouveau nom du fichier téléchargé.
     */
    public function uploadImage($file, string $destination): string
    {
        try {
            // Obtenir le nom de fichier original
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            // Générer un nom unique pour éviter les conflits
            $newFilename = uniqid() . '.' . $file->guessExtension();

            // Déplacer le fichier dans le répertoire de destination
            $file->move($destination, $newFilename);
            return $newFilename;
        } catch (\Exception $e) {
            dump('Erreur lors du téléchargement du fichier : ' . $e->getMessage());
            throw new ('Erreur lors du téléchargement du fichier : ' . $e->getMessage());
        }
    }

    /**
     * Ajoute une demande de fonds dans le système.
     * Cela inclut la création d'une nouvelle demande et son enregistrement dans la base de données.
     *
     * @param string $exercice L'exercice fiscal.
     * @param int $planCptEntityId L'ID de l'entité du plan comptable.
     * @param int $planCptMotifId L'ID du motif du plan comptable.
     * @param float $montantDemande Le montant demandé.
     * @param string $modePaiement Le mode de paiement.
     * @param string $dateSaisie La date à laquelle la demande a été créée.
     * @param string $dateOperation La date de l'opération.
     * @return array Le statut et le message de la demande.
     */
    public function addDemandeFonds($exercice, $planCptEntityId, $planCptMotifId, $montantDemande, $modePaiement, $dateSaisie, $dateOperation)
    {

        $entityManager = $this->demandeTypeRepository->getEntityManager();
        $entityManager->beginTransaction();

        try {
            $demande = $this->demandeRepository->findDemandeByCode(10);             // Code demande 10 => Decaissement

            $demande_type = new DemandeType();
            $demande_type->setDmMontant($montantDemande);

            // Définit l'entité et le motif de la demande
            $entity_code = $this->planCompteRepo->find($planCptEntityId);       // getPlanCompte ENTITE by ID
            $plan_compte_motif = $this->planCompteRepo->find($planCptMotifId);  // getPlanCompteMotif by ID

            $demande_type->setEntityCode($entity_code);
            $demande_type->setDmModePaiement($modePaiement);
            $demande_type->setDmEtat($this->etatDmRepo, 100);                 // 100 Initié OK_ETAT
            $demande_type->setUtilisateur($this->user);
            $demande_type->setPlanCompte($plan_compte_motif);
            $demande_type->setExercice($exercice);
            $demande_type->setDemande($demande);
            $demande_type->setDmDate(new \DateTime($dateSaisie));
            $demande_type->setDmDateOperation(new \DateTime($dateOperation));

            // Persiste la demande et met à jour avec une référence
            $entityManager->persist($demande_type);                             // Ajout de demandes de fonds
            $entityManager->flush();

            $demande_type_reference = $this->createReferenceForId($demande->getDmCode(), $demande_type->getId());// reference demande
            $demande_type->setRefDemande($demande_type_reference);                                              // update avec reference

            $entityManager->flush();
            $entityManager->commit();

        } catch (\Throwable $th) {
            $entityManager->rollback();
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur de demande de fonds : ' . $th->getMessage()
            ]);
        }
        return [
            "status" => true,
            "message" => 'Demande de fonds insérer',
        ];
    }

    /**
     * Génère une référence pour une demande en fonction du type et de l'ID.
     *
     * @param int $typeReference Le type de la demande.
     * @param int $Id L'ID de la demande.
     * @return string La référence générée.
     */
    public function createReferenceForId($typeReference, $Id)
    {
        switch ($typeReference) {
            case 10:
                return "DEC/" . date('Y') . "/" . $Id;
                break;
            case 20:
                return "APR/" . date('Y') . "/" . $Id;
                break;
        }
    }

    /**
     * Récupère toutes les demandes de types initiées par l'utilisateur actuel.
     *
     * @return array Liste des demandes de types de l'utilisateur actuel.
     */
    public function findAllMyDemandeTypesInit()
    {
        $data = $this->demandeTypeRepository->findByUtilisateur($this->user);
        return $data;
    }

    /**
     * Récupère les logs de toutes les demandes effectuées par l'utilisateur actuel.
     *
     * @return array Liste des logs pour les demandes de l'utilisateur actuel.
     */
    public function findAllMyDemandeWithLog()
    {
        $temp_user = new Utilisateur($this->user);
        $user_matricule = $temp_user->getUserMatricule();
        $logs_demande = $this->logDmRepository->findLogsForUserMatricule($user_matricule);
        return $logs_demande;
    }

    // Insertion d'approvisionnement

    /**
     * Récupère toutes les demandes effectuées par l'utilisateur actuel, y compris celles avec des logs.
     *
     * @return array Liste de toutes les demandes de l'utilisateur actuel, y compris celles avec des logs.
     */
    public function findAllMyDemande()
    {
        $all_demande = $this->demandeTypeRepository->findAll(); // toute les demandes
        $user_matricule = $this->user->getUserMatricule();
        $my_demande = [];

        foreach ($all_demande as $dm) {
            if ($dm->getUtilisateur()->getUserMatricule() == $user_matricule) {   // si anazy ilay demandes
                array_push($my_demande, $dm);
            } else {                                                               //si tsia dia asesy ny log
                $dm_logs = $dm->getLogDemandeTypes();
                foreach ($dm_logs as $log) {
                    if ($log->getUserMatricule() == $user_matricule) {
                        array_push($my_demande, $dm);
                        // return;
                    }
                }
            }
        }
        return $my_demande;
    }

    /**
     * Ajoute une demande d'approvisionnement.
     * Utilise une logique similaire à celle de la méthode addDemandeFonds.
     *
     * @param string $exercice L'exercice fiscal.
     * @param int $planCptEntityId L'ID de l'entité du plan comptable.
     * @param int $planCptMotifId L'ID du motif du plan comptable.
     * @param float $montantDemande Le montant demandé.
     * @param string $modePaiement Le mode de paiement.
     * @param string $dateSaisie La date à laquelle la demande a été créée.
     * @param string $dateOperation La date de l'opération.
     * @return array Le statut de l'opération.
     */
    public function insertDemandeTypeAppro($exercice, $planCptEntityId, $montantDemande, $modePaiement, $dateSaisie, $dateOperation, int $tresorier_user_id): JsonResponse
    {
        $demande = $this->demandeRepository->findDemandeByCode(20);                                 // Code demande 20 => Approvisionnement

        $entityManager = $this->demandeTypeRepository->getEntityManager();
        $entityManager->beginTransaction();

        try {
            $demande_type = new DemandeType();
            $demande_type->setDmMontant($montantDemande);
            $entity_code_and_plan_compte_motif = $this->planCompteRepo->find($planCptEntityId);     // getPlanCompte ENTITE by ID
            $demande_type->setEntityCode($entity_code_and_plan_compte_motif);
            $demande_type->setDmModePaiement($modePaiement);
            $demande_type->setDmEtat($this->etatDmRepo, 300);                                     // 500 Comptabilisation OK_ETAT
            $demande_type->setUtilisateur($this->user);
            $demande_type->setPlanCompte($entity_code_and_plan_compte_motif);
            $demande_type->setExercice($exercice);
            $demande_type->setDemande($demande);
            $demande_type->setDmDate(new \DateTime($dateSaisie));
            $demande_type->setDmDateOperation(new \DateTime($dateOperation));

            $entityManager->persist($demande_type);
            $entityManager->flush();

            $demande_type_reference = $this->createReferenceForId($demande->getDmCode(), $demande_type->getId());// reference demande
            $demande_type->setRefDemande($demande_type_reference);                                              // update avec reference
            $entityManager->flush();

            // Comptabilisation de l'approvisionnement
            // les données à utiliser
            $user_tresorier = $entityManager->find(Utilisateur::class, $tresorier_user_id);
            $reference_demande = $demande_type->getRefDemande();
            $exercice_demande = $this->exercicerepository->getExerciceValide();
            $montant_demande = $demande_type->getDmMontant();
            $numero_compte_debit = $demande_type->getPlanCompte();
            $transaction_a_faire = $this->trsTypeRepo->findTransactionForApprovision();                                         // identifier le type de transaction à faire
            $detail_transaction = $this->detailTrsRepo->findByTransactionWithTypeOperation($transaction_a_faire, 0);            // identifier le mouvement à créditer

            // Création evenement
            $evenement = new Evenement($transaction_a_faire, $user_tresorier, $exercice_demande, $demande_type->getEntityCode()->getCptLibelle(), $montant_demande, $reference_demande, new DateTime());
            $entityManager->persist($evenement);
            // Création des mouvements

            $mv_debit = new Mouvement($evenement, $numero_compte_debit, $montant_demande, true);                        // DEBIT
            $entityManager->persist($mv_debit);

            $mv_credit = new Mouvement($evenement, $detail_transaction->getPlanCompte(), $montant_demande, false);      // CREDIT
            $entityManager->persist($mv_credit);

            $entityManager->flush();
            $entityManager->commit();                           // si tout OK
            return new JsonResponse([
                "success" => true,
                "message" => 'Approvisionnement insérer',
                "ref_approvisionnement" => $evenement->getEvnReference()
            ]);
        } catch (\Throwable $th) {
            $entityManager->rollback();                         // si erreur opération
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur création approvisionnement : ' . $th->getMessage(),
                "ref_approvisionnement" => null
            ]);
        }

    }

    // Mis à jour de l'état d'une demande 

    /**
     * Met à jour une demande de fonds existante.
     *
     * Cette méthode permet de mettre à jour le montant et/ou le compte de dépense associés à une demande de fonds existante.
     * Si aucune modification n'est effectuée, un message indiquant qu'aucun changement n'a été effectué sera retourné.
     * En cas de modification, un historique des modifications (log) est créé et l'état de la demande est mis à jour.
     * Si une erreur se produit lors de la mise à jour, une exception sera levée et la transaction sera annulée.
     *
     * @param int $id_demande_fonds L'ID de la demande de fonds à mettre à jour.
     * @param float|string $demande_montant_nouveau Le nouveau montant de la demande de fonds. Si la valeur est "non", aucune modification n'est effectuée sur le montant.
     * @param int $id_compte_depense L'ID du nouveau compte de dépense à associer à la demande de fonds. Si la valeur est -1, aucun changement de compte de dépense n'est effectué.
     *
     * @return array Tableau associatif contenant :
     *      - 'status' (bool) : Indique si l'opération a été réussie.
     *      - 'update' (bool) : Indique si une mise à jour a été effectuée.
     *      - 'message' (string) : Message expliquant le résultat de l'opération.
     *
     * @throws InvalidArgumentException Si une erreur se produit lors de la mise à jour ou si un argument est invalide.
     */
    public function updateDemandeFonds($id_demande_fonds, $demande_montant_nouveau, $id_compte_depense)
    {
        // Récupération de l'Entity Manager et début de la transaction
        $em = $this->demandeTypeRepository->getEntityManager();
        $em->beginTransaction();
        try {
            $status_update_compte = false;

            // Récupération de la demande de fonds existante
            $demande_fonds = $this->demandeTypeRepository->find($id_demande_fonds);             // trouver le demande actuel
            if ($demande_montant_nouveau != "non") {
                $demande_montant_nouveau = (float)$demande_montant_nouveau; // pour avoir un montant zéro ou null
            }
            if ($demande_montant_nouveau == "non") {                          // on doit vérifier si la valeur du montant est non
                $demande_montant_nouveau = $demande_fonds->getDmMontant();
            }
            $status_update_montant = $demande_fonds->setDmMontant($demande_montant_nouveau);    // update nouveau montant

            // Si un nouveau compte de dépense est spécifié (id différent de -1), mettre à jour le compte de dépense
            if ((int)($id_compte_depense) != -1) {                                                // si on change
                $compte_depense_nouveau = $this->planCompteRepo->find($id_compte_depense);      // nouveau compte dépense 
                $demande_fonds->setPlanCompte($compte_depense_nouveau);
                $status_update_compte = true;
            }

            // Si aucune modification n'a été effectuée, retourner un message indiquant qu'il n'y a pas de changement
            if ($status_update_montant == false && $status_update_compte == false) {
                return [
                    "status" => true,
                    "update" => false,
                    "message" => "Aucun changement effectué !",
                ];
            }

            // Enregistrement d'un log pour l'historique de la demande
            $user_sg = $demande_fonds->getUtilisateur();

            // Insérer Attente modification dans Historique des demandes
            $log_dm = new LogDemandeType();
            $log_dm->setDmEtat($this->etatDmRepo, $demande_fonds->getDmEtat()); // Historisation de la demande OK_ETAT
            $log_dm->setUserMatricule($user_sg->getUserMatricule());
            $log_dm->setDemandeType($demande_fonds);
            $log_dm->setLogDmDate(new DateTime());
            $em->persist($log_dm);

            // Mise à jour de l'état de la demande de fonds (état de modification)
            $demande_fonds->setDmEtat($this->etatDmRepo, 101); // Modification de l'état de la demande
            $demande_fonds->setUtilisateur($this->user);  // ajout de l'utilisateur
            $demande_fonds->setDmDate($log_dm->getLogDmDate()); // MAJ de dm_type la base de données

            $em->flush();
            $em->commit();

            // Création de logs et

            return [
                "status" => true,
                "update" => true,
                "message" => sprintf('Modification réussi'),
            ];
        } catch (InvalidArgumentException $th) {
            // En cas d'erreur, annulation de la transaction
            $em->rollback();
            return [
                'status' => false,
                'message' => $th->getMessage()
            ];
        }

    }


}