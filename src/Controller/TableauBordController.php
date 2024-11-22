<?php

namespace App\Controller;

use App\Repository\DetailBudgetRepository;
use App\Repository\ExerciceRepository;
use App\Repository\MouvementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tableau_bord')]
class TableauBordController extends AbstractController
{
    #[Route('/', name: 'app_tableau_bord')]
    public function index(): Response
    {
        return $this->render('tableau_bord/index.html.twig', [
            'controller_name' => 'TableauBordController',
        ]);
    }

    #[Route('/budget', name: 'app_tableau_budget', methods: ['GET', "POST"])]
    public function budget(Request $request, ExerciceRepository $exerciceRepository, DetailBudgetRepository $detailBudgetRepository): Response
    {
        $exe1 = $exerciceRepository->getExerciceValide()->getId();
        //$exe2 = $exerciceRepository->getExerciceNext(new \DateTime())[0]->getId();
        //dump($exe2);
        $exe2 = $exerciceRepository->getExerciceValide()->getId();
        $exe1 = $request->query->get('exe1', $exe1);
        $exe2 = $request->query->get('exe2', $exe2);
        $labels = ["4", "5", "6"];
        $list_label = [];
        foreach ($labels as $label) {
            $list_label[] = $detailBudgetRepository->determinerCategorie($label);
        }
        //dump($exe1 ." ". $exe2);
        $somme_exercice1 = $detailBudgetRepository->findSommeParCompte($exerciceRepository->find($exe1));
        $categories_1 = [];

        $somme_exercice2 = $detailBudgetRepository->findSommeParCompte($exerciceRepository->find($exe2));
        $categories_2 = [];

        // Remplir les catégories existantes avec les valeurs de la requête
        foreach ($somme_exercice1 as $somme) {
            $categories_1[$somme['categorie']] = $somme['total_budget'];
        }

        // Remplir les catégories existantes avec les valeurs de la requête
        foreach ($somme_exercice2 as $somme) {
            $categories_2[$somme['categorie']] = $somme['total_budget'];
        }
        return $this->render('tableau_bord/graphe_comparaison_budget.html.twig', [
            'labels' => $list_label,
            'exercice1' => $categories_1,
            'exercice2' => $categories_2,
            'list_exercice' => $exerciceRepository->findAll(),
        ]);
    }

    #[Route('/depense', name: 'app_tableau_depense', methods: ['GET', "POST"])]
    public function depense(Request                $request,
                            ExerciceRepository     $exerciceRepository,
                            MouvementRepository    $mouvementRepository,
                            DetailBudgetRepository $detailBudgetRepository): Response
    {
        $exercice = $exerciceRepository->getExerciceValide();
        $espece = $mouvementRepository->v_debit_caisse_annuel($exercice);
        $cheques = $mouvementRepository->v_debit_banque_annuel($exercice);
        $budget = $detailBudgetRepository->findSommeParExerciceEtCompte($exercice, "6");

        $espece = 70000 + 100000 + 30000 + 50000 + 90000 + 75000;
        $cheques = 200000 + 100000 + 100000 + 200000 + 90000 + 500000 + 150000;
        $budget = 200000 + 100000 + 100000 + 200000 + 90000 + 500000 + 150000;

        return $this->render('tableau_bord/diagramme_camembert_depense.html.twig', [
            'labels' => ["Espèces", "Chèques"],
            'budget' => $budget,
            'total_depense' => $espece + $cheques,
            'data' => [$espece, $cheques]
        ]);
    }

    #[Route('/depense/annuelle', name: 'app_tableau_depense_annuelle', methods: ['GET', "POST"])]
    public function depense_annuelle(ExerciceRepository     $exerciceRepository,
                                     MouvementRepository    $mouvementRepository,
                                     DetailBudgetRepository $detailBudgetRepository): Response
    {
        $exercice = $exerciceRepository->getExerciceValide();
        $somme_debit_banque = $mouvementRepository->v_debit_banque_mensuel($exercice);
        $somme_debit_caisse = $mouvementRepository->v_debit_caisse_mensuel($exercice);
        $budgets = $detailBudgetRepository->findSommeParExerciceEtCompte($exercice, "6");

        $depense = [];
        $budget = [];
        $depense[0] = ($somme_debit_banque[0] ?? 0) + ($somme_debit_caisse[0] ?? 0);
        //ALANA ITO RANDY
//        $budget[0] = $budgets;
        $budget[0] = 200000 + 100000 + 100000 + 200000 + 90000 + 500000 + 150000;

        for ($i = 1; $i < 12; $i++) {
            //ALANA ITO RANDY
            //$budget[$i] = $budgets;
            $budget[$i] = 200000 + 100000 + 100000 + 200000 + 90000 + 500000 + 150000;
            $depense[$i] = $depense[$i - 1] + ($somme_debit_banque[$i] ?? 0) + ($somme_debit_caisse[$i] ?? 0);
        }

        $depense[0] = 270000;
        $depense[1] = $depense[0] + 200000;
        $depense[2] = $depense[1] + 130000;
        $depense[3] = $depense[2] + 300000;
        $depense[4] = $depense[3] + 250000;
        $depense[5] = $depense[4] + 140000;
        $depense[6] = $depense[5] + 590000;
        $depense[7] = $depense[6] + 225000;
        $depense[8] = $depense[7] + 0;
        $depense[9] = $depense[8] + 120000;
        $depense[10] = $depense[9] + 0;
        $depense[11] = $depense[10] + 0;


        return $this->render('tableau_bord/courbe_depense.html.twig', [
            'labels' => ["Jan", "Fev", "Mars", "Avr", "Mai", "Juin", "Juillet", "Aou", "Sep", "Oct", "Nov", "Dec"],
            'budget' => $budget,
            'data' => $depense
        ]);
    }
}
