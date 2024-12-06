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

    /**
     * Récupère la liste des entités correspondant à un type de demande spécifique.
     *
     * Cette méthode permet de récupérer toutes les entités qui sont associées à un type de demande donné
     * (représenté par l'objet `DemandeType`). Elle filtre les résultats en fonction de l'attribut `demande_type`
     * de l'entité, en cherchant les enregistrements qui correspondent à la valeur du type de demande passé en paramètre.
     *
     * @param DemandeType $demandeType L'objet `DemandeType` représentant le type de demande à rechercher.
     *
     * @return array|null Un tableau d'entités correspondant à ce type de demande, ou `null` si aucun enregistrement
     *         n'est trouvé.
     */
    public function findByDemandeType(DemandeType $demandeType): ?array
    {
        return $this->createQueryBuilder('l')
            ->Where('l.demande_type = :val')
            ->setParameter('val', $demandeType)
            ->getQuery()
            ->getResult();
    }

    // Transactionnel

    /**
     * Valide une demande en associant un utilisateur à un type de demande et en enregistrant un log de validation.
     *
     * Cette méthode permet de valider une demande en deux étapes principales :
     * 1. L'utilisateur de type demande est assigné à une demande.
     * 2. Un log de validation est créé pour enregistrer cette action.
     *
     * Si une des étapes échoue (par exemple, un type de demande ou un utilisateur introuvable),
     * la transaction est annulée et un message d'erreur est retourné.
     *
     * La méthode est transactionnelle, ce qui garantit que toutes les opérations dans la transaction
     * réussissent ou échouent ensemble. En cas d'échec d'une des opérations, la transaction est annulée.
     *
     * @param int $dm_type_id L'ID du type de demande à valider.
     * @param int $sg_user_id L'ID de l'utilisateur SG à associer à la demande.
     *
     * @return JsonResponse Une réponse JSON indiquant le succès ou l'échec de la validation de la demande.
     *         - Si la validation échoue à cause d'un type de demande ou utilisateur introuvable, une réponse d'erreur est retournée.
     *         - En cas de succès, un message de confirmation est renvoyé.
     */
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

    /**
     * Refuse une demande en mettant à jour son état et en enregistrant un log de refus.
     *
     * Cette méthode permet de refuser une demande en modifiant l'état de la demande, en ajoutant un commentaire de refus,
     * et en enregistrant un log de cette action. Elle garantit que toutes les opérations sont effectuées de manière transactionnelle,
     * ce qui signifie que si une erreur se produit durant le processus, aucune modification ne sera enregistrée.
     *
     * Si une des étapes échoue (par exemple, un type de demande ou un utilisateur introuvable, ou si le commentaire est vide),
     * la transaction est annulée et un message d'erreur est retourné.
     *
     * La méthode est transactionnelle et toutes les modifications sont validées ou annulées en bloc.
     *
     * @param int $dm_type_id L'ID du type de demande à refuser.
     * @param int $sg_user_id L'ID de l'utilisateur SG qui refuse la demande.
     * @param string $commentaire_data Le commentaire de refus à ajouter au log.
     *
     * @return JsonResponse Une réponse JSON indiquant le succès ou l'échec de l'opération :
     *         - Si la validation échoue à cause d'un type de demande ou utilisateur introuvable, ou si le commentaire est vide, une réponse d'erreur est retournée.
     *         - En cas de succès, un message de confirmation est renvoyé.
     */
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
                $log_dm->setDmEtat($this->etatDmRepository, $dm_type->getDmEtat()); // OK_ETAT État actuel de la demande
                $log_dm->setUserMatricule($user_demande->getUserMatricule());
                $log_dm->setLogDmObservation($commentaire_data);
                $log_dm->setDemandeType($dm_type);
                $log_dm->setLogDmDate(new \DateTime());
                $entityManager->persist($log_dm);

                // Mise à jour de l'état de la demande pour marquer le refus
                $dm_type->setDmEtat($this->etatDmRepository, 301);                 // OK_ETAT : Refuser État de demande "Refusé"
                $dm_type->setUtilisateur($user_sg); // Affectation de l'utilisateur SG qui effectue le refus
                $dm_type->setDmDate(new \DateTime());
                $entityManager->persist($dm_type);

                // Accepter les changements Validation de la transaction
                $entityManager->flush();
                $entityManager->commit();                                           // Si tout se passe bien, on valide les modifications

            } catch (\Exception $e) {
                // Si une erreur se produit, la transaction est annulée
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
        // Si tout se passe bien, retour d'une réponse JSON de succès
        return new JsonResponse([
            'success' => true,
            'message' => 'La demande a été refusée avec succès.'

        ]);
    }

    // Transactionnel

    /**
     * Modifie une demande en enregistrant un log de modification et en mettant à jour l'état de la demande.
     *
     * Cette méthode permet de modifier une demande en changeant son état et en enregistrant un commentaire expliquant la modification.
     * Elle est transactionnelle, ce qui signifie que si une erreur survient pendant le processus, aucune modification ne sera enregistrée.
     *
     * Si l'une des étapes échoue (par exemple, si le type de demande, l'utilisateur SG ou l'utilisateur associé à la demande sont introuvables,
     * ou si le commentaire est vide), la transaction est annulée et un message d'erreur est retourné.
     *
     * La méthode est transactionnelle, donc toutes les modifications de la demande et de son log sont effectuées ensemble : si l'une échoue,
     * tout est annulé.
     *
     * @param int $dm_type_id L'ID du type de demande à modifier.
     * @param int $sg_user_id L'ID de l'utilisateur SG qui effectue la modification de la demande.
     * @param string $commentaire_data Le commentaire expliquant la modification de la demande.
     *
     * @return JsonResponse Une réponse JSON contenant un message de succès ou d'échec de l'opération :
     *         - Si une erreur se produit lors de la récupération des entités ou du traitement de la demande, une réponse d'erreur est retournée.
     *         - Si la modification réussit, un message de confirmation est renvoyé.
     */
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
            // Validation du commentaire
            if ($commentaire_data === null || empty(trim($commentaire_data))) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Le message ne peut pas être vide.'
                ]);
            }

            // Démarrage de la transaction
            $entityManager->beginTransaction();
            try {
                // Création du log
                $log_dm = new LogDemandeType();
                $log_dm->setDmEtat($this->etatDmRepository, $dm_type->getDmEtat()); // État actuel de la demande
                $log_dm->setUserMatricule($user_demande->getUserMatricule());
                $log_dm->setLogDmObservation($commentaire_data); // Ajout du commentaire de modification
                $log_dm->setDemandeType($dm_type);
                $log_dm->setLogDmDate(new DateTime());
                $entityManager->persist($log_dm);

                // MAJ de dm_type la base de données
                $dm_type->setDmEtat($this->etatDmRepository, 201); // Mise à jour de l'état de la demande
                $dm_type->setUtilisateur($user_sg);
                $dm_type->setDmDate(new \DateTime());
                $entityManager->persist($dm_type);

                // Validation des changements
                $entityManager->flush();
                $entityManager->commit();

                // Retour de la réponse en cas de succès
                return new JsonResponse([
                    'success' => true,
                    'message' => 'La demande a été modifié avec succès.'

                ]);
            } catch (\Exception $e) {
                $entityManager->rollback();
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Erreur lors de l insertion du log : ' . $e->getMessage()
                ]);
            }
        } catch (\Exception $e) {

            // Gestion des erreurs générales
            $entityManager->rollback();
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la modification de la demande : ' . $e->getMessage()
            ]);

        }

    }

    // Transactionnel

    /**
     * Gère le processus de déblocage de fonds pour une demande spécifiée.
     * Cette méthode inclut la validation des informations de la demande,
     * l'ajout des données relatives à un chèque ou à un autre mode de paiement,
     * ainsi que la comptabilisation de la transaction correspondante.
     *
     * @param int $dm_type_id L'ID du type de demande de déblocage de fonds.
     * @param int $tresorier_user_id L'ID de l'utilisateur (trésorier) qui effectue le déblocage.
     * @param int|null $banque_id L'ID de la banque associée (facultatif, nécessaire uniquement si le paiement est effectué par chèque).
     * @param string|null $numero_cheque Le numéro du chèque à utiliser pour le paiement (facultatif).
     * @param string|null $remettant Le nom du remettant du chèque (facultatif).
     * @param string|null $beneficiaire Le nom du bénéficiaire du chèque (facultatif).
     *
     * @return JsonResponse Retourne une réponse JSON :
     *      - 'success' : booléen indiquant si l'opération a réussi ou échoué.
     *      - 'message' : message décrivant le résultat de l'opération (succès ou erreur).
     *
     * @throws \Doctrine\ORM\Exception\ORMException Lève une exception si une erreur se produit au niveau de la gestion des entités.
     * @throws \Doctrine\ORM\OptimisticLockException Lève une exception si un conflit de verrouillage optimiste se produit lors de la gestion des entités.
     */

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
     * Récupère la liste des logs pour un utilisateur donné.
     *
     * Cette méthode permet de retrouver tous les logs associés à un utilisateur
     * à partir de son matricule. Elle utilise le matricule de l'utilisateur pour
     * rechercher les logs dans la base de données.
     *
     * La recherche des logs se fait en filtrant les résultats par le matricule
     * de l'utilisateur dans la table des logs.
     *
     * @param string $userMatricule Le matricule de l'utilisateur pour lequel
     *                              on souhaite récupérer les logs.
     *
     * @return array Un tableau contenant les logs associés à l'utilisateur.
     *               Chaque élément du tableau correspond à un log.
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
