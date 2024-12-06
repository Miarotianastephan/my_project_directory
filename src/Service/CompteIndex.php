<?php

use App\Entity\CompteMere;
use App\Entity\PlanCompte;

class CompteIndex
{
    private array $compteIndexe = [];

    public function __construct(array $rawData)
    {
        $this->processRawDataToIndexe($rawData);
        $this->findParent();
    }

    /**
     * Traite les données brutes et les indexe dans un tableau.
     *
     * Cette méthode crée des objets `PlanCompte` pour chaque ligne de données brutes, puis
     * les ajoute à la collection `$compteIndexe` en utilisant le numéro de compte comme clé.
     *
     * @param array $rawData Tableau contenant les données brutes des comptes à indexer.
     *
     * @return void
     */
    private function processRawDataToIndexe(array $rawData): void
    {
        // Parcours chaque ligne dans les données brutes
        foreach ($rawData as $row) {
            // Extraction des informations nécessaires de chaque ligne
            $numero = $row['N° de compte'];
            $intitule = $row['Intitullé'];

            // Création d'un objet PlanCompte et affectation des valeurs
            $planCompte = new PlanCompte();
            $planCompte->setCptNumero($numero);
            $planCompte->setCptLibelle($intitule);

            // Ajout du plan de compte à la collection indexée par son numéro
            $this->compteIndexe[$numero] = $planCompte;
        }
    }

    /**
     * Recherche les comptes mères et les associe aux plans de comptes correspondants.
     *
     * Cette méthode parcourt tous les comptes indexés. Pour chaque plan de compte, elle tente de
     * trouver un compte mère en utilisant une logique basée sur les numéros de compte. Si un parent
     * est trouvé, il est associé au plan de compte. Si aucun parent n'est trouvé, le compte lui-même
     * devient son propre parent.
     *
     * @return void
     */
    private function findParent()
    {
        // Parcours de chaque plan de compte
        foreach ($this->compteIndexe as $compte) {
            if ($compte->getCompteMere() == null) {
                $numeroCompte = $compte->getCptNumero();
                $length = strlen($numeroCompte);

                // Recherche du compte mère en parcourant les sous-ensembles du numéro de compte
                for ($i = $length - 1; $i > 1; $i--) {
                    $parentNumero = substr($numeroCompte, 0, $i);
                    if (isset($this->compteIndexe[$parentNumero]) && $compte->getCompteMere() == null) {
                        // Création du parent de la mère trouvé avec son fils
                        // Ajout du compte mère au plan de compte actuel
                        $this->addParentToCompte($numeroCompte, $parentNumero);
                        // dump($this->compteIndexe[$numeroCompte]);
                    }
                }

                // Si aucun parent n'est trouvé et que la longueur du numéro est supérieure à 2,
                // le compte devient son propre parent
                if ($compte->getCompteMere() == null && $length > 2) { // no_parent et que sa longueur est ing_égale à minimale
                    // Création du parent de la mère =  fils
                    $this->addParentToCompte($numeroCompte, $numeroCompte);
                }
            }
        }
    }


    /**
     * Ajoute un parent à un plan de compte donné.
     *
     * Cette méthode crée un objet `CompteMere` pour le parent trouvé et l'associe au plan de compte
     * donné en le liant avec la méthode `setCompteMere`.
     *
     * @param string $compte Le numéro du plan de compte à associer avec un parent.
     * @param string $parentNumero Le numéro du compte mère à associer au plan de compte.
     *
     * @return void
     */
    private function addParentToCompte($compte, $parentNumero)
    {
        // Création du parent de la mère trouvé avec son fils
        $compteMere = new CompteMere();
        $compteMere->setCptNumero($parentNumero);
        $intitule = $this->compteIndexe[$parentNumero]->getCptLibelle();
        $compteMere->setCptLibelle($intitule);

        // Association du compte mère au plan de compte
        $this->compteIndexe[$compte]->setCompteMere($compteMere); // mère des comptes actuelle
    }

}
