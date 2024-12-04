<?php

namespace App\Repository;

use App\Entity\Banque;
use App\Entity\DemandeType;
use App\Entity\Evenement;
use App\Entity\LogDemandeType;
use App\Entity\Mouvement;
use App\Entity\UsageCheque;
use App\Entity\Utilisateur;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<LogDemandeType>
 */
class LogDemandeTypeRepository extends ServiceEntityRepository
{

    private $etatDmRepository;
    private $trsTypeRepo;
    private $detailTrsRepo;
    private $utilisateurRepository;
    private $exercicerepository;

    public function __construct(ManagerRegistry $registry, EtatDemandeRepository $etatDmRepo, TransactionTypeRepository $trsTypeRepo, DetailTransactionCompteRepository $detailTrsRepo, UtilisateurRepository $utilisateurRepo, ExerciceRepository $exoRepo)
    {
        $this->etatDmRepository = $etatDmRepo;
        $this->trsTypeRepo = $trsTypeRepo;
        $this->detailTrsRepo = $detailTrsRepo;
        $this->utilisateurRepository = $utilisateurRepo;
        $this->exercicerepository = $exoRepo;
        parent::__construct($registry, LogDemandeType::class);
    }

    public function findByDemandeType(DemandeType $demandeType): ?array
    {
        return $this->createQueryBuilder('l')
            ->Where('l.demande_type = :val')
            ->setParameter('val', $demandeType)
            ->getQuery()
            ->getResult();
    }

    // Transactionnel
    public function ajoutValidationDemande(int $dm_type_id, int $sg_user_id): JsonResponse
    {
        $entityManager = $this->getEntityManager();
        try {
            // Récupération des entités
            $dm_type = $entityManager->find(DemandeType::class, $dm_type_id);
            if (!$dm_type) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Demande de type introuvable'
                ]);
            }

            $user_sg = $entityManager->find(Utilisateur::class, $sg_user_id);
            if (!$user_sg) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Utilisateur SG introuvable.'
                ]);
            }

            $user_demande = $dm_type->getUtilisateur();
            if (!$user_demande) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Utilisateur associé à la demande introuvable.'
                ]);
            }

            $entityManager->beginTransaction();
            try {
                // Création du log
                $log_dm = new LogDemandeType();
                $log_dm->setDmEtat($this->etatDmRepository, $dm_type->getDmEtat()); // OK_ETAT
                $log_dm->setUserMatricule($user_demande->getUserMatricule());
                $log_dm->setDemandeType($dm_type);
                $log_dm->setLogDmDate(new \DateTime());
                $entityManager->persist($log_dm);

                // Mise à jour de l'entité `DemandeType`
                $dm_type->setDmEtat($this->etatDmRepository, 200);
                $dm_type->setUtilisateur($user_sg);
                $dm_type->setDmDate(new \DateTime());
                $entityManager->persist($dm_type);

                $entityManager->flush();
                $entityManager->commit();
            } catch (\Exception $e) {
                $entityManager->rollback();
                throw new \Exception('Erreur lors de l\'insertion du log : ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            // Gestion de l'exception générale et retour d'une réponse JSON d'erreur
            $entityManager->rollback();
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la validation de la demande : ' . $e->getMessage()
            ]);
        }

        // Si tout se passe bien, retour d'une réponse JSON de succès
        return new JsonResponse([
            'success' => true,
            'message' => 'La demande a été validée',
        ]);
    }

    // Transactionnel
    public function ajoutRefuserDemande(int $dm_type_id, int $sg_user_id, string $commentaire_data): JsonResponse
    {
        $entityManager = $this->getEntityManager();

        try {
            // Récupération des entités
            $dm_type = $entityManager->find(DemandeType::class, $dm_type_id);
            if (!$dm_type) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Demande de type introuvable'
                ]);
            }
            $user_sg = $entityManager->find(Utilisateur::class, $sg_user_id);
            if (!$user_sg) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Utilisateur SG introuvable.'
                ]);
            }
            $user_demande = $dm_type->getUtilisateur();
            if (!$user_demande) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Utilisateur associé à la demande introuvable.'
                ]);
            }
            if ($commentaire_data === null || empty(trim($commentaire_data))) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Le message ne peut pas être vide.'
                ]);
            }

            $entityManager->beginTransaction();
            try {
                // Création du log
                $log_dm = new LogDemandeType();
                $log_dm->setDmEtat($this->etatDmRepository, $dm_type->getDmEtat()); // OK_ETAT
                $log_dm->setUserMatricule($user_demande->getUserMatricule());
                $log_dm->setLogDmObservation($commentaire_data);
                $log_dm->setDemandeType($dm_type);
                $log_dm->setLogDmDate(new \DateTime());
                $entityManager->persist($log_dm);

                // Update de la ligne de demande
                // $dm_type->setDmEtat($this->etatDmRepository, 300);                 // OK_ETAT : Refuser
                $dm_type->setDmEtat($this->etatDmRepository, 301);                 // OK_ETAT : Refuser
                $dm_type->setUtilisateur($user_sg);
                $dm_type->setDmDate(new \DateTime());
                $entityManager->persist($dm_type);

                // Accepter les changements  
                $entityManager->flush();
                $entityManager->commit();                                           // si tout OK 

            } catch (\Exception $e) {
                $entityManager->rollBack();
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Erreur lors de l\'insertion du log : ' . $e->getMessage()
                ]);
            }

        } catch (\Exception $e) {
            // Gestion de l'exception et retour d'une réponse JSON d'erreur
            $entityManager->rollBack();
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors du refus de la demande : ' . $e->getMessage()
            ]);
        }
        // Retour d'une réponse JSON de succès
        return new JsonResponse([
            'success' => true,
            'message' => 'La demande a été refusée avec succès.'

        ]);
    }

    // Transactionnel
    public function ajoutModifierDemande(int $dm_type_id, int $sg_user_id, string $commentaire_data): JsonResponse
    {
        $entityManager = $this->getEntityManager();

        try {
            // Récupération des entités
            $dm_type = $entityManager->find(DemandeType::class, $dm_type_id);
            if (!$dm_type) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Demande de type introuvable'
                ]);
            }
            $user_sg = $entityManager->find(Utilisateur::class, $sg_user_id);
            if (!$user_sg) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Utilisateur SG introuvable.'
                ]);
            }
            $user_demande = $dm_type->getUtilisateur();
            if (!$user_demande) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Utilisateur associé à la demande introuvable.'
                ]);
            }
            if ($commentaire_data === null || empty(trim($commentaire_data))) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Le message ne peut pas être vide.'
                ]);
            }

            $entityManager->beginTransaction();
            try {
                // Création du log
                $log_dm = new LogDemandeType();
                $log_dm->setDmEtat($this->etatDmRepository, $dm_type->getDmEtat());
                $log_dm->setUserMatricule($user_demande->getUserMatricule());
                $log_dm->setLogDmObservation($commentaire_data);
                $log_dm->setDemandeType($dm_type);
                $log_dm->setLogDmDate(new DateTime());
                $entityManager->persist($log_dm);

                // MAJ de dm_type la base de données
                $dm_type->setDmEtat($this->etatDmRepository, 201);
                $dm_type->setUtilisateur($user_sg);
                $dm_type->setDmDate(new \DateTime());
                $entityManager->persist($dm_type);

                $entityManager->flush();
                $entityManager->commit();

            } catch (\Exception $e) {
                $entityManager->rollback();
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Erreur lors de l insertion du log : ' . $e->getMessage()
                ]);
            }
        } catch (\Exception $e) {
            $entityManager->rollback();
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la modification de la demande : ' . $e->getMessage()
            ]);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'La demande a été modifié avec succès.'

        ]);
    }

    // Transactionnel
    public function ajoutDeblockageFond(int $dm_type_id, int $tresorier_user_id, int $banque_id = null, string $numero_cheque = null, string $remettant = null, string $beneficiaire = null): JsonResponse
    {
        $entityManager = $this->getEntityManager();
        $dm_type = $entityManager->find(DemandeType::class, $dm_type_id);
        if (!$dm_type) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Déblocage impossible car demande introuvable'
            ]);
        }

        // Debut de transaction de béblocages de fonds
        $entityManager->beginTransaction();
        try {

            $dm_mode_paiement = $dm_type->getDmModePaiement();
            //if == 1 -> payement par chèque
            if ($dm_mode_paiement == 1) {
                if ($banque_id == null) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Ajouter un choix de banque'
                    ]);
                } else if (!is_numeric($numero_cheque)) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Le numéro de chèque doit être un entier'
                    ]);
                } else if ($numero_cheque == null || empty(trim($numero_cheque))) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Ajouter un numero de chèque'
                    ]);
                } else if ($remettant === null) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Ajouter un remettant de chèque'
                    ]);
                } else if ($beneficiaire === null) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Ajouter un beneficiaire de chèque'
                    ]);
                }
                $banque = $entityManager->find(Banque::class, $banque_id);
                if ($banque === null) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Ajouter une banque valide'
                    ]);
                }
                //Chèque
                $cheque = new UsageCheque();
                $cheque->setChqMontant($dm_type->getDmMontant());
                $cheque->setIsValid(true);
                $cheque->setChqBeneficiaire($beneficiaire);
                $cheque->setChqRemettant($remettant);
                $cheque->setChqNumero($numero_cheque);
                $cheque->setDateUsage(new \DateTime());
                $cheque->setBanque($banque);
                $entityManager->persist($cheque);
            }

            $user_tresorier = $entityManager->find(Utilisateur::class, $tresorier_user_id);
            if (!$user_tresorier) {                                 // Vérifier si non-trésorier
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Utilisateur trésorier introuvable.'
                ]);
            }
            $user_sg = $dm_type->getUtilisateur();                  // Vérifier si SG nanao validation
            if (!$user_sg) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Validateur du demande introuvable.'
                ]);
            }

            // Insérer Validé dans Historique des demandes
            $log_dm = new LogDemandeType();
            $log_dm->setDmEtat($this->etatDmRepository, $dm_type->getDmEtat());                     // Historisation de la demande OK_ETAT
            $log_dm->setUserMatricule($user_sg->getUserMatricule());
            $log_dm->setDemandeType($dm_type);
            $log_dm->setLogDmDate(new DateTime());
            $entityManager->persist($log_dm);

            // Update Validé => Débloqué dans les demandes
            // $dm_type->setDmEtat($this->etatDmRepository, 301);                                      // Déblocage de la demande OK_ETAT
            $dm_type->setDmEtat($this->etatDmRepository, 300);                                      // Déblocage de la demande OK_ETAT
            $dm_type->setUtilisateur($user_tresorier);
            $dm_type->setDmDate($log_dm->getLogDmDate());                                           // MAJ de dm_type la base de données

            // Comptabilisation
            // les données à utiliser
            $reference_demande = $dm_type->getRefDemande();
            $exercice_demande = $this->exercicerepository->getExerciceValide();
            if (!$exercice_demande) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Exercice invalide.'
                ]);
            }
            $montant_demande = $dm_type->getDmMontant();
            $numero_compte_debit = $dm_type->getPlanCompte();
            $mode_paiement_demande = (int)($dm_type->getDmModePaiement());
            $transaction_a_faire = $this->trsTypeRepo->findTransactionByModePaiement($mode_paiement_demande);   // identifier le type de transaction à faire

            // dump($detail_transaction);
            // Création evenement 
            $evenement = new Evenement($transaction_a_faire, $user_tresorier, $exercice_demande, $dm_type->getEntityCode()->getCptLibelle(), $montant_demande, $reference_demande, new DateTime());
            $entityManager->persist($evenement);
            // Création des mouvements


            $mv_debit = new Mouvement($evenement, $numero_compte_debit, $montant_demande, true);// DEBIT
            $entityManager->persist($mv_debit);

            $detail_transaction_credit = $this->detailTrsRepo->findByTransactionWithTypeOperation($transaction_a_faire, 0);                // identifier le mouvement à réaliser
            if ($detail_transaction_credit === null) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Compte à créditer introuvable pour ' . $transaction_a_faire->getTrsCode()
                ]);
            }
            $mv_credit = new Mouvement($evenement, $detail_transaction_credit->getPlanCompte(), $montant_demande, false);// CREDIT
            $entityManager->persist($mv_credit);

            dump("numero de debit = " . $numero_compte_debit->getCptNumero() . " libelle" . $numero_compte_debit->getCptLibelle());
            dump("numero de credit = " . $detail_transaction_credit->getPlanCompte()->getCptNumero() . " libelle" . $detail_transaction_credit->getPlanCompte()->getCptLibelle());


            $entityManager->flush();
            $entityManager->commit();
            return new JsonResponse([
                'success' => true,
                'message' => 'Traitement de fonds : débloqué et comptabiliser'
            ]);// si tout OK

        } catch (\Exception $e) {
            dump($e->getMessage());
            $entityManager->rollback();                         // si erreur opération
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur transaction : ' . $e->getMessage()
            ]);
        }

    }

    /**
     * Pour avoir la liste des log pour une utilisateur
     * Dia alaina avy ato ny demandeType niaviny
     * Dia alaina @ demandeType ny log Rehetra avy eo
     * @param $userMatricule
     * @return array
     */
    public function findLogsForUserMatricule($userMatricule): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.user_matricule = :val')
            ->setParameter('val', $userMatricule)
            ->getQuery()
            ->getResult();
    }

}
