<?php

use App\Entity\CompteMere;
use App\Entity\PlanCompte;

class CompteHierarchyService
{
    private array $compteMeres = [];
    private array $planComptes = [];

    public function __construct(array $rawData)
    {
        $this->processRawData($rawData);
    }

    /**
     * Est utilisé dans l'import de donné à partir d'un fichier excel
     * Traite les données brutes et les enregistre dans les entités appropriées.
     *
     * Cette méthode parcourt les données brutes (chaque ligne contenant un numéro et un intitulé de compte),
     * et selon la longueur du numéro de compte, elle décide si un `CompteMere` ou un `PlanCompte` doit être ajouté.
     *
     * @param array $rawData array $rawData Tableau contenant les données brutes avec des informations sur les comptes (numéro, intitulé).
     *
     * @return void
     */
    private function processRawData(array $rawData): void
    {
        // Parcours de chaque ligne dans les données brutes
        foreach ($rawData as $row) {
            // Récupération des informations nécessaires de chaque ligne
            $numero = $row['N° de compte'];
            $intitule = $row['Intitullé'];

            // Si le numéro de compte est inférieur ou égal à 3 caractères, c'est un CompteMere
            if (strlen($numero) <= 3) {
                $this->addCompteMere($numero, $intitule);
            } else {
                // Sinon, c'est un PlanCompte
                $this->addPlanCompte($numero, $intitule);
            }
        }
    }

    /**
     * Ajoute un nouveau compte mère à la collection.
     *
     * Crée un objet `CompteMere`, lui attribue un numéro de compte et un intitulé,
     * puis l'ajoute à la collection des comptes mères, indexée par le numéro du compte.
     *
     * @param string $numero Le numéro du compte mère à ajouter.
     * @param string $intitule L'intitulé ou libellé du compte mère à ajouter.
     *
     * @return void
     */
    private function addCompteMere(string $numero, string $intitule): void
    {
        // Création d'un nouveau compte mère
        $compteMere = new CompteMere();

        // Attribution des valeurs au compte mère
        $compteMere->setCptNumero($numero);
        $compteMere->setCptLibelle($intitule);

        // Ajout du compte mère à la collection, indexée par le numéro de compte
        $this->compteMeres[$numero] = $compteMere;
    }

    /**
     * Ajoute un plan de compte à la collection et le lie au compte mère correspondant.
     *
     * Cette méthode crée un objet `PlanCompte`, lui attribue un numéro et un intitulé,
     * recherche le numéro du compte mère correspondant, et si un compte mère est trouvé,
     * lie ce plan de compte au compte mère. Ensuite, le plan de compte est ajouté à la
     * collection des plans de comptes, indexée par son numéro.
     *
     * @param string $numero Le numéro du plan de compte à ajouter.
     * @param string $intitule L'intitulé ou libellé du plan de compte à ajouter.
     *
     * @return void
     */
    private function addPlanCompte(string $numero, string $intitule): void
    {
        $planCompte = new PlanCompte();
        $planCompte->setCptNumero($numero);
        $planCompte->setCptLibelle($intitule);

        $parentNumero = $this->findParentNumero($numero);
        if (isset($this->compteMeres[$parentNumero])) {
            $planCompte->setCompteMere($this->compteMeres[$parentNumero]);
            $this->compteMeres[$parentNumero]->addPlanCompte($planCompte);
        }

        $this->planComptes[$numero] = $planCompte;
    }

    /**
     * Trouve le numéro du compte mère à partir du numéro du plan de compte.
     *
     * Cette méthode recherche le compte mère correspondant à un plan de compte donné.
     * Elle parcourt les sous-ensembles du numéro de compte pour trouver la correspondance
     * dans la collection des comptes mères.
     *
     * @param string $numero Le numéro du plan de compte pour lequel on recherche le compte mère.
     *
     * @return string Le numéro du compte mère trouvé, ou le premier chiffre du numéro de compte si aucun parent n'est trouvé.
     */
    private function findParentNumero(string $numero): string
    {
        $length = strlen($numero);
        for ($i = $length; $i > 1; $i--) {
            $parentNumero = substr($numero, 0, $i);
            if (isset($this->compteMeres[$parentNumero])) {
                return $parentNumero;
            }
        }
        return substr($numero, 0, 1);  // Fallback to first digit if no parent found
    }

    /**
     * Retourne la hiérarchie complète des comptes sous forme de tableau.
     *
     * Cette méthode parcourt la collection des comptes mères et pour chaque compte mère,
     * elle construit une hiérarchie en ajoutant les plans de comptes associés.
     *
     * @return array La hiérarchie des comptes sous forme de tableau.
     */
    public function getHierarchy(): array
    {
        $hierarchy = [];
        foreach ($this->compteMeres as $compteMere) {
            $hierarchy[] = $this->buildCompteHierarchy($compteMere);
        }
        return $hierarchy;
    }

    /**
     * Construit la hiérarchie des comptes pour un compte mère donné.
     *
     * Cette méthode prend un `CompteMere` et construit un tableau représentant la hiérarchie,
     * avec le numéro, l'intitulé du compte mère et la liste de ses plans de comptes enfants.
     *
     * @param CompteMere $compteMere Le compte mère dont on veut construire la hiérarchie.
     *
     * @return array La hiérarchie du compte mère sous forme de tableau.
     */
    private function buildCompteHierarchy(CompteMere $compteMere): array
    {
        $children = [];
        foreach ($compteMere->getPlanComptes() as $planCompte) {
            $children[] = [
                'numero' => $planCompte->getCptNumero(),
                'intitule' => $planCompte->getCptLibelle(),
            ];
        }

        return [
            'numero' => $compteMere->getCptNumero(),
            'intitule' => $compteMere->getCptLibelle(),
            'children' => $children,
        ];
    }

}
