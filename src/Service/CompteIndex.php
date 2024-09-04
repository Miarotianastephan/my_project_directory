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

    private function processRawDataToIndexe(array $rawData): void
    {
        foreach ($rawData as $row) {
            $numero = $row['N° de compte'];
            $intitule = $row['Intitullé'];
            
            $planCompte = new PlanCompte();
            $planCompte->setCptNumero($numero);
            $planCompte->setCptLibelle($intitule);

            $this->compteIndexe[$numero] = $planCompte;
        }
        // dump(['data'=> $this->compteIndexe]);
    }

    private function findParent(){

        foreach($this->compteIndexe as $compte){
            if($compte->getCompteMere() == null){
                $numeroCompte = $compte->getCptNumero();
                $length = strlen($numeroCompte);
                for ($i = $length-1; $i > 1; $i--) {
                    $parentNumero = substr($numeroCompte, 0, $i);
                    if ( isset($this->compteIndexe[$parentNumero])  && $compte->getCompteMere() == null) {
                        // Création du parent du mère trouvé avec son fils
                        $this->addParentToCompte($numeroCompte, $parentNumero);
                        // dump($this->compteIndexe[$numeroCompte]);
                    }
                }
                if($compte->getCompteMere() == null && $length > 2){ // no_parent et que sa longueur est ing_égale à minimale
                    // Création du parent du mère =  fils
                    $this->addParentToCompte($numeroCompte, $numeroCompte);
                }
            }
        }
        dump($this->compteIndexe);

    }

    private function addParentToCompte($compte,$parentNumero){
        // Création du parent du mère trouvé avec son fils
        $compteMere = new CompteMere();
        $compteMere->setCptNumero($parentNumero);
        $intitule = $this->compteIndexe[$parentNumero]->getCptLibelle();
        $compteMere->setCptLibelle($intitule);
        $this->compteIndexe[$compte]->setCompteMere($compteMere); // mère du comptes actuel
    }

}
