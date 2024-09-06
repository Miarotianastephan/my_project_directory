<?php

namespace App\Controller;

use App\Entity\DetailTransactionCompte;
use App\Entity\PlanCompte;
use App\Entity\TransactionType;
use App\Repository\DemandeTypeRepository;
use App\Repository\PlanCompteRepository;
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
    public function index(Request $request): Response
    {
        $annee = $request->query->get('annee', (int)date('Y'));
        $mois = $request->query->get('mois', 'janvier');

        // Ici, vous devriez récupérer les vraies données en fonction de $annee et $mois
        // Ceci est juste un exemple
        $labels = ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin"];
        $fond = [100, 150, 200, 250, 300, 350];
        $caisse = [120, 140, 180, 220, 260, 300];
        $sold = [130, 160, 210, 240, 290, 310];

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'annee' => $annee,
                'mois' => $mois,
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
            'mois' => $mois
        ]);
    }

    #[Route('/form/depense', name: 'comptable.form_depense_directe', methods: ['GET'])]
    public function form_depense_directe(PlanCompteRepository $planCompteRepository): Response
    {
        $trType1 = new TransactionType();
        $trType1->setId(1);
        $trType1->setTrsCode('CE-007');
        $trType1->setTrsDefinition(null);
        $trType1->setTrsLibelle('Dépense payées directement pas BFM');

        $trType2 = new TransactionType();
        $trType2->setId(2);
        $trType2->setTrsCode('CE-011');
        $trType2->setTrsDefinition(null);
        $trType2->setTrsLibelle('Comptabilisation de frais bancaire');

        $liste_transaction_type = [$trType1, $trType2];

        $plc1 = new PlanCompte(1, "510001", "Caisse siège");
        $plc2 = new PlanCompte(2, "510002", "Caisse RT ATB");
        $plc3 = new PlanCompte(3, "510003", "Caisse RT ATR");
        $plc4 = new PlanCompte(4, "510004", "Caisse RT FNR");
        $plc5 = new PlanCompte(5, "510005", "Caisse RT MDV");
        $plc6 = new PlanCompte(6, "510006", "Caisse RT MHJ");
        $plc7 = new PlanCompte(7, "510007", "Caisse RT MNK");
        $plc8 = new PlanCompte(8, "510008", "Caisse RT SBV");
        $plc9 = new PlanCompte(9, "510009", "Caisse RT TLG");
        $plc10 = new PlanCompte(10, "510010", "Caisse RT TLR");
        $plc11 = new PlanCompte(11, "510011", "Caisse RT TMS");

        $pl3 = new PlanCompte(14, "67", "Frais et commission bancaire");
        $pl4 = new PlanCompte(15, "670001", "Frais de tenue de compte");
        $pl5 = new PlanCompte(16, "670002", "Frais de demande de chequier");
        $pl6 = new PlanCompte(17, "670003", "Frais de retrait");
        $pl7 = new PlanCompte(18, "670004", "Autres frais bancaires");
        $pl8 = new PlanCompte(19, "670005", "Autres charges");

        $pl2 = new PlanCompte(13, "442750", "Autres materiels");
        $pl9 = new PlanCompte(20, "442710", "Materiels de bureau");
        $pl10 = new PlanCompte(21, "442720", "Mobilier de bureau");
        $pl11 = new PlanCompte(22, "442730", "Materiels informatiques");
        $pl12 = new PlanCompte(23, "442740", "Materiles de communication");


        $detail_transaction_cpt_1 = new DetailTransactionCompte();
        $detail_transaction_cpt_1->setId(1);
        $detail_transaction_cpt_1->setTransactionType(new ArrayCollection([$trType1]));
        $detail_transaction_cpt_1->setPlanCompte(new ArrayCollection([$pl2, $pl9, $pl10, $pl11, $pl12, $pl3, $pl4, $pl5, $pl6, $pl7, $pl8]));

        $detail_transaction_cpt_2 = new DetailTransactionCompte();
        $detail_transaction_cpt_2->setId(22);
        $detail_transaction_cpt_2->setTransactionType(new ArrayCollection([$trType2]));
        $detail_transaction_cpt_2->setPlanCompte(new ArrayCollection([$pl3, $pl4, $pl5, $pl6, $pl7, $pl8]));

        $detail_transaction_cpt = [$detail_transaction_cpt_1, $detail_transaction_cpt_2];

        $liste_entite = [$plc1, $plc2, $plc3, $plc4, $plc5, $plc6, $plc7, $plc8, $plc9, $plc10, $plc11];

        $transactionCompteMap = [];
        foreach ($detail_transaction_cpt as $detail) {
            foreach ($detail->getTransactionType() as $transactionType) {
                $transactionCompteMap[$transactionType->getId()] = array_map(function($compte) {
                    return [
                        'id' => $compte->getId(),
                        'numero' => $compte->getCptNumero(),
                        'libelle' => $compte->getCptLibelle()
                    ];
                }, $detail->getPlanCompte()->toArray());
            }
        }

        return $this->render('comptable/ajout_dep_direct.html.twig',
            [
                'liste_entite' => $liste_entite,
                'list_opp' => $liste_transaction_type,
                'transactionCompteMap' => json_encode($transactionCompteMap)
            ]
        );
    }

    #[Route('/comptabilisation', name: 'comptable.suivi_comptabilisation', methods: ['GET'])]
    public function suivi_comptabilisation( DemandeTypeRepository $dm_rep): Response
    {
        $demande_types = $dm_rep->findByEtat(70);
        return $this->render('comptable/suivi_comptabilisation.html.twig',['demande_types'=>$demande_types]);
    }

}