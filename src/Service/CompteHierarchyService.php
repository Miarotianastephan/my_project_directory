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

    private function processRawData(array $rawData): void
    {
        foreach ($rawData as $row) {
            $numero = $row['N° de compte'];
            $intitule = $row['Intitullé'];

            if (strlen($numero) <= 3) {
                $this->addCompteMere($numero, $intitule);
            } else {
                $this->addPlanCompte($numero, $intitule);
            }
        }
    }

    private function addCompteMere(string $numero, string $intitule): void
    {
        $compteMere = new CompteMere();
        $compteMere->setCptNumero($numero);
        $compteMere->setCptLibelle($intitule);
        $this->compteMeres[$numero] = $compteMere;
    }

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

    public function getHierarchy(): array
    {
        $hierarchy = [];
        foreach ($this->compteMeres as $compteMere) {
            $hierarchy[] = $this->buildCompteHierarchy($compteMere);
        }
        return $hierarchy;
    }

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
