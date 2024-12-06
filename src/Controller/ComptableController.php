<?php

namespace App\Controller;

use App\Repository\DetailTransactionCompteRepository;
use App\Repository\EvenementRepository;
use App\Repository\ExerciceRepository;
use App\Repository\MouvementRepository;
use App\Repository\PlanCompteRepository;
use App\Repository\TransactionTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comptable')]
class ComptableController extends AbstractController
{
    private $user;

    /**
     * Constructeur du contrôleur ComptableController.
     * Initialise l'utilisateur connecté pour pouvoir l'utiliser dans les autres méthodes.
     *
     * @param Security $security Le service de sécurité pour récupérer l'utilisateur connecté.
     */
    public function __construct(Security $security)
    {
        // $this->security = $security;
        $this->user = $security->getUser();
    }

    /**
     * Affiche le graphique des mouvements comptables pour une année et un semestre donnés.
     *
     * @param Request $request La requête HTTP pour obtenir les paramètres de l'année et du semestre.
     * @param MouvementRepository $mouvementRepository Le repository pour récupérer les mouvements bancaires et de caisse.
     * @param ExerciceRepository $exerciceRepository Le repository pour récupérer l'exercice fiscal valide.
     *
     * @return Response La réponse HTTP avec les données de graphique pour l'affichage.
     */
    #[Route('/', name: 'comptable.graphe', methods: ['GET', 'POST'])]
    public function index(Request $request, MouvementRepository $mouvementRepository, ExerciceRepository $exerciceRepository): Response
    {
        $annee = $request->query->get('annee', (int)date('Y'));
        $semestre = $request->query->get('semestre', (int)'1');
        $exercice = $exerciceRepository->getExerciceValide();
        $somme_debit_banque = $mouvementRepository->v_debit_banque_mensuel($exercice);
        $somme_debit_caisse = $mouvementRepository->v_debit_caisse_mensuel($exercice);
        //dump($somme_debit_banque);
        $message = null;
        if ($exercice || $somme_debit_banque == null || $somme_debit_caisse == null) {
            $message = "Dashboard invalide";
        }

        // Ici, vous devriez récupérer les vraies données en fonction de $annee et $mois
        if ($semestre == 1) {
            $labels = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin"];
            $fond = [100, 150, 200, 250, 300, 350];
            $caisse = [$somme_debit_banque[0] ?? 0, $somme_debit_banque[1] ?? 0, $somme_debit_banque[3] ?? 0, $somme_debit_banque[4] ?? 0, $somme_debit_banque[5] ?? 0];
            $sold = [$somme_debit_caisse[0] ?? 0, $somme_debit_caisse[1] ?? 0, $somme_debit_caisse[3] ?? 0, $somme_debit_caisse[4] ?? 0, $somme_debit_caisse[5] ?? 0];
        }
        if ($semestre == 2) {
            $labels = ["Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Décembre"];
            $fond = [];
            $caisse = [$somme_debit_banque[6] ?? 0, $somme_debit_banque[7] ?? 0, $somme_debit_banque[8] ?? 0, $somme_debit_banque[9] ?? 0, $somme_debit_banque[10] ?? 0, $somme_debit_banque[11] ?? 0];
            $sold = [$somme_debit_caisse[6] ?? 0, $somme_debit_caisse[7] ?? 0, $somme_debit_caisse[8] ?? 0, $somme_debit_caisse[9] ?? 0, $somme_debit_caisse[10] ?? 0, $somme_debit_caisse[11] ?? 0];
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'annee' => $annee,
                'semestre' => $semestre,
                'labels' => $labels,
                'fond' => $fond,
                'caisse' => $caisse,
                'sold' => $sold,
            ]);
        }

        return $this->render('comptable/graphe.html.twig', [
            'labels' => $labels,
            'fond' => $fond,
            'caisse' => $caisse,
            'sold' => $sold,
            'annee' => $annee,
            'semestre' => $semestre,
            'message' => $message
        ]);
    }

    /**
     * Affiche le formulaire pour ajouter une dépense directe.
     *
     * @param PlanCompteRepository $planCompteRepository Le repository pour récupérer les plans de compte pour la caisse.
     * @param TransactionTypeRepository $transactionTypeRepository Le repository pour récupérer les types de transactions pour les dépenses directes.
     *
     * @return Response La réponse HTTP avec le formulaire d'ajout de dépense directe.
     */
    #[Route('/form/depense', name: 'comptable.form_depense_directe', methods: ['GET'])]
    public function form_depense_directe(
        PlanCompteRepository      $planCompteRepository,
        TransactionTypeRepository $transactionTypeRepository
    ): Response
    {
        $liste_entite = $planCompteRepository->findCompteCaisse();
        $liste_transaction = $transactionTypeRepository->findTransactionDepenseDirecte();

        return $this->render('comptable/ajout_dep_direct.html.twig', [
            'message' => null,
            'liste_entite' => $liste_entite,
            'list_opp' => $liste_transaction
        ]);
    }

    /**
     * Retourne les détails des transactions pour un type de transaction donné.
     *
     * @param Request $request La requête HTTP contenant l'ID de la transaction.
     * @param TransactionTypeRepository $transactionTypeRepository Le repository pour récupérer le type de transaction.
     * @param DetailTransactionCompteRepository $detailTransactionCompteRepository Le repository pour récupérer les détails associés à la transaction.
     *
     * @return JsonResponse La réponse JSON contenant les détails des transactions.
     */
    #[Route('/get-transaction-details', name: 'get_transaction_details', methods: ['GET'])]
    public function getTransactionDetails(
        Request                           $request,
        TransactionTypeRepository         $transactionTypeRepository,
        DetailTransactionCompteRepository $detailTransactionCompteRepository
    ): JsonResponse
    {
        $transactionId = $request->query->get('transactionId');
        $transaction = $transactionTypeRepository->find($transactionId);

        if (!$transaction) {
            return new JsonResponse(['error' => 'Transaction not found'], 404);
        }

        $details = $detailTransactionCompteRepository->findAllByTransaction($transaction);
        $formattedDetails = array_map(function ($detail) {
            return [
                'id' => $detail->getPlanCompte()->getId(),
                'numero' => $detail->getPlanCompte()->getCptNumero(),
                'libelle' => $detail->getPlanCompte()->getCptLibelle(),
            ];
        }, $details);
        return new JsonResponse($formattedDetails);
    }

    /**
     * Valide l'ajout d'une dépense directe et effectue les vérifications nécessaires.
     *
     * @param Request $request La requête HTTP contenant les données du formulaire.
     * @param PlanCompteRepository $planCompteRepository Le repository pour récupérer les plans de compte.
     * @param TransactionTypeRepository $transactionTypeRepository Le repository pour récupérer les types de transaction.
     * @param DetailTransactionCompteRepository $detailTransactionCompteRepository Le repository pour récupérer les détails de transaction.
     *
     * @return Response La réponse HTTP après validation de la dépense directe.
     */
    #[Route('/valider/depense', name: 'comptable.validation_depense_directe', methods: ['POST'])]
    public function validation_depense_directe(Request                           $request,
                                               PlanCompteRepository              $planCompteRepository,
                                               TransactionTypeRepository         $transactionTypeRepository,
                                               DetailTransactionCompteRepository $detailTransactionCompteRepository)
    {
        // Récupère les données du formulaire
        $data = $request->request->all();
        $entite_id = $data['entite'] ?? null;
        $transaction_id = $data['transaction'] ?? null;
        $montant = $data['montant'] ?? null;
        $planCompte_id = $data['plan_compte'] ?? null;

        if (!$planCompte_id) {
            $liste_entite = $planCompteRepository->findCompteCaisse();
            $liste_transaction = $transactionTypeRepository->findTransactionDepenseDirecte();
            return $this->render('comptable/ajout_dep_direct.html.twig', [
                'message' => "Veuillez completer tous les champs.",
                'liste_entite' => $liste_entite,
                'list_opp' => $liste_transaction
            ]);
        } else if (!$montant) {
            $liste_entite = $planCompteRepository->findCompteCaisse();
            $liste_transaction = $transactionTypeRepository->findTransactionDepenseDirecte();
            return $this->render('comptable/ajout_dep_direct.html.twig', [
                'message' => "Le montant est nécessaire.",
                'liste_entite' => $liste_entite,
                'list_opp' => $liste_transaction
            ]);
        } else if (!$transaction_id) {
            $liste_entite = $planCompteRepository->findCompteCaisse();
            $liste_transaction = $transactionTypeRepository->findTransactionDepenseDirecte();
            return $this->render('comptable/ajout_dep_direct.html.twig', [
                'message' => "Choix de transaction nécessaire.",
                'liste_entite' => $liste_entite,
                'list_opp' => $liste_transaction
            ]);

        } else if (!$entite_id) {
            $liste_entite = $planCompteRepository->findCompteCaisse();
            $liste_transaction = $transactionTypeRepository->findTransactionDepenseDirecte();
            return $this->render('comptable/ajout_dep_direct.html.twig', [
                'message' => "Choix de l'entité est nécessaire",
                'liste_entite' => $liste_entite,
                'list_opp' => $liste_transaction
            ]);

        } else if ($montant <= 0) {
            $liste_entite = $planCompteRepository->findCompteCaisse();
            $liste_transaction = $transactionTypeRepository->findTransactionDepenseDirecte();
            return $this->render('comptable/ajout_dep_direct.html.twig', [
                'message' => "Le montant doit être un chiffre positif.",
                'liste_entite' => $liste_entite,
                'list_opp' => $liste_transaction
            ]);
        }
        $entite = $planCompteRepository->find($entite_id);

        if (!$entite) {
            $liste_entite = $planCompteRepository->findCompteCaisse();
            $liste_transaction = $transactionTypeRepository->findTransactionDepenseDirecte();
            return $this->render('comptable/ajout_dep_direct.html.twig', [
                'message' => "Entité est introuvable",
                'liste_entite' => $liste_entite,
                'list_opp' => $liste_transaction
            ]);
        }

        $transaction = $transactionTypeRepository->find($transaction_id);
        if (!$transaction) {
            $liste_entite = $planCompteRepository->findCompteCaisse();
            $liste_transaction = $transactionTypeRepository->findTransactionDepenseDirecte();
            return $this->render('comptable/ajout_dep_direct.html.twig', [
                'message' => "Transaction introuvable.",
                'liste_entite' => $liste_entite,
                'list_opp' => $liste_transaction
            ]);

        }
        $planCompte = $planCompteRepository->find($entite_id);
        if (!$planCompte) {
            $liste_entite = $planCompteRepository->findCompteCaisse();
            $liste_transaction = $transactionTypeRepository->findTransactionDepenseDirecte();

            return $this->render('comptable/ajout_dep_direct.html.twig', [
                'message' => "blabla",
                'liste_entite' => $liste_entite,
                'list_opp' => $liste_transaction
            ]);

        }
        $date = new \DateTime();

        $compte_debit = $planCompteRepository->find($planCompte_id);
        if (!$compte_debit) {
            $liste_entite = $planCompteRepository->findCompteCaisse();
            $liste_transaction = $transactionTypeRepository->findTransactionDepenseDirecte();

            return $this->render('comptable/ajout_dep_direct.html.twig', [
                'message' => "Compte de débit associé introuvable.",
                'liste_entite' => $liste_entite,
                'list_opp' => $liste_transaction
            ]);

        }
        $compte_credit = $detailTransactionCompteRepository->findPlanCompte_CreditByTransaction($transaction);
        if (!$compte_credit) {
            $liste_entite = $planCompteRepository->findCompteCaisse();
            $liste_transaction = $transactionTypeRepository->findTransactionDepenseDirecte();

            return $this->render('comptable/ajout_dep_direct.html.twig', [
                'message' => "Compte de crédit associé introuvale.",
                'liste_entite' => $liste_entite,
                'list_opp' => $liste_transaction
            ]);

        }

        return $this->render('comptable/validation_dep_direct.html.twig',
            [
                'success' => true,
                'entite' => $entite,
                'transaction' => $transaction,
                'plan_compte' => $planCompte,
                'montant' => $montant,
                'debit' => $compte_debit,
                'credit' => $compte_credit,
                'date' => $date,

            ]);
    }

    /**
     * Ajoute une dépense directe dans la base de données.
     *
     * Cette méthode valide les données envoyées dans le corps de la requête,
     * puis appelle la méthode `comptabilisation_directe` du repository pour enregistrer l'opération dans la base de données.
     *
     * @Route("/comptabilisation_directe", name="comptable.comptabilisation_directe", methods={"POST"})
     *
     * @param Request $request La requête HTTP contenant les données de la dépense directe.
     * @param MouvementRepository $mouvementRepository Le repository pour gérer les mouvements comptables.
     *
     * @return JsonResponse La réponse JSON avec le statut de l'opération et un message de confirmation ou d'erreur.
     */
    #[Route('/comptabilisation_directe', name: 'comptable.comptabilisation_directe', methods: ['post'])]
    public function comptabilisation_directe(Request             $request,
                                             MouvementRepository $mouvementRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $date = $data['date'] ?? null;
        $entite = $data['entite'] ?? null;
        $transaction = $data['transaction'] ?? null;
        $compte_debit = $data['compte_debit'] ?? null;
        $compte_credit = $data['compte_credit'] ?? null;
        $montant = $data['montant'] ?? null;
        if (!$date) {
            return new JsonResponse(['success' => false, 'message' => 'La date est invalide']);
        } elseif (!$entite) {
            return new JsonResponse(['success' => false, 'message' => 'L\'entite est invalide']);
        } elseif (!$transaction) {
            return new JsonResponse(['success' => false, 'message' => 'La transaction est invalide']);
        } elseif (!$compte_debit) {
            return new JsonResponse(['success' => false, 'message' => 'Le compte de debit est invalide']);
        } elseif (!$compte_credit) {
            return new JsonResponse(['success' => false, 'message' => 'Le compte de credit est invalide']);
        } elseif (!$montant) {
            return new JsonResponse(['success' => false, 'message' => 'Le montant de credit est invalide']);
        }

        $id_user_comptable = $this->user->getId();
        $reponse = $mouvementRepository->comptabilisation_directe($date, $entite, $transaction, $compte_debit, $compte_credit, $montant, $id_user_comptable);
        $reponse = json_decode($reponse->getContent(), true);
        return new JsonResponse([
            'success' => $reponse['success'],
            'message' => $reponse['message'],
            'url' => $this->generateUrl('comptable.form_depense_directe')
        ]);
    }

    /**
     * Page de liste des opérations directe.
     */
    #[Route('/comptabilisation', name: 'comptable.suivi_comptabilisation', methods: ['GET'])]
    public function suivi_comptabilisation(EvenementRepository $evn_repo): Response
    {
        $list_operation_directe = $evn_repo->findEvnByResponsable($this->user);
        return $this->render('comptable/suivi_comptabilisation.html.twig', ['list_operation_directe' => $list_operation_directe]);
    }

    /**
     * Page de détails d'une opération directe.
     */
    #[Route('/comptabilisation/{id}', name: 'comptable.suivi_comptabilisation_detail', methods: ['GET'])]
    public function compatbilisation(int $id, EvenementRepository $evn_repo, MouvementRepository $mv_repo): Response
    {
        $evenement = $evn_repo->find($id);
        $list_mouvement = $mv_repo->findAllMvtByEvenement($evenement);
        return $this->render(
            'comptable/show_comptabilisation.html.twig',
            ['evenement' => $evenement,
                'list_mouvement' => $list_mouvement
            ]);
    }


}