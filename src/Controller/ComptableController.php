<?php

namespace App\Controller;

use App\Entity\DetailTransactionCompte;
use App\Entity\Exercice;
use App\Entity\PlanCompte;
use App\Entity\TransactionType;
use App\Repository\DemandeTypeRepository;
use App\Repository\DetailTransactionCompteRepository;
use App\Repository\ExerciceRepository;
use App\Repository\MouvementRepository;
use App\Repository\PlanCompteRepository;
use App\Repository\TransactionTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/comptable')]
class ComptableController extends AbstractController
{
    #[Route('/', name: 'comptable.graphe', methods: ['GET', 'POST'])]
    public function index(Request $request,MouvementRepository $mouvementRepository, ExerciceRepository $exerciceRepository): Response
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
        // Ceci est juste un exemple
        if ($semestre == 1) {
            $labels = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin"];
            $fond = [100, 150, 200, 250, 300, 350];
            $caisse = [$somme_debit_banque[0] ?? 0,$somme_debit_banque[1] ?? 0,$somme_debit_banque[3] ?? 0,$somme_debit_banque[4] ?? 0,$somme_debit_banque[5] ?? 0];
            $sold = [$somme_debit_caisse[0] ?? 0,$somme_debit_caisse[1] ?? 0,$somme_debit_caisse[3] ?? 0,$somme_debit_caisse[4] ?? 0,$somme_debit_caisse[5] ?? 0];
        }
        if ($semestre == 2) {
            $labels = ["Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Décembre"];
            $fond = [];
            $caisse = [$somme_debit_banque[6] ?? 0,$somme_debit_banque[7] ?? 0,$somme_debit_banque[8] ?? 0,$somme_debit_banque[9] ?? 0,$somme_debit_banque[10] ?? 0,$somme_debit_banque[11] ?? 0];
            $sold = [$somme_debit_caisse[6] ?? 0,$somme_debit_caisse[7] ?? 0,$somme_debit_caisse[8] ?? 0,$somme_debit_caisse[9] ?? 0,$somme_debit_caisse[10] ?? 0,$somme_debit_caisse[11] ?? 0];
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

    #[Route('/form/depense', name: 'comptable.form_depense_directe', methods: ['GET'])]
    public function form_depense_directe(
        PlanCompteRepository              $planCompteRepository,
        TransactionTypeRepository         $transactionTypeRepository
    ): Response
    {
        $liste_entite = $planCompteRepository->findCompteCaisse();
        $liste_transaction = $transactionTypeRepository->findTransactionDepenseDirecte();

        return $this->render('comptable/ajout_dep_direct.html.twig', [
            'liste_entite' => $liste_entite,
            'list_opp' => $liste_transaction
        ]);
    }

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
        //$transaction = $transactionTypeRepository->findTransactionByCode("CE-007");
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


    #[Route('/valider/depense', name: 'comptable.validation_depense_directe', methods: ['POST'])]
    public function validation_depense_directe(Request                           $request,
                                               PlanCompteRepository              $planCompteRepository,
                                               TransactionTypeRepository         $transactionTypeRepository,
                                               DetailTransactionCompteRepository $detailTransactionCompteRepository): Response
    {
        // Récupère les données du formulaire
        $entite_id = $request->request->get('entite');
        $entite = $planCompteRepository->find($entite_id);


        $transaction_id = $request->request->get('transaction');
        $transaction = $transactionTypeRepository->find($transaction_id);


        $planCompte = $planCompteRepository->find($entite_id);

        $montant = $request->request->get('montant');
        $date = new \DateTime();

        $planCompte_id = $request->request->get('plan_compte');
        $compte_debit = $planCompteRepository->find($planCompte_id);

        $compte_credit = $detailTransactionCompteRepository->findPlanCompte_CreditByTransaction($transaction);
        return $this->render('comptable/validation_dep_direct.html.twig',
            [
                'entite' => $entite,
                'transaction' => $transaction,
                'planCompte' => $planCompte,
                'montant' => $montant,
                'debit' => $compte_debit,
                'credit' => $compte_credit,
                'date' => $date,
            ]);
    }


    #[Route('/comptabilisation', name: 'comptable.suivi_comptabilisation', methods: ['GET'])]
    public function suivi_comptabilisation(DemandeTypeRepository $dm_rep): Response
    {
        $demande_types = $dm_rep->findByEtat(null,[401]);
        return $this->render('comptable/suivi_comptabilisation.html.twig', ['demande_types' => $demande_types]);
    }

    #[Route('/comptabilisation/{id}', name: 'comptable.comptabiliser', methods: ['GET'])]
    public function compatbilisation(int $id, DemandeTypeRepository $dm_rep): Response
    {
        $demande_types = $dm_rep->find($id);
        return $this->render('comptable/show_comptabilisation.html.twig', ['demande_type' => $demande_types, 'images' => []]);
    }


}