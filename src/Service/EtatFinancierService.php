<?php

namespace App\Service;

use App\Entity\CompteMere;
use App\Repository\CompteMereRepository;
use App\Repository\ExerciceRepository;

class EtatFinancierService
{
    public CompteMere $compteMere;
    public $detailsMontant = [];        // liste d'enfants avec chaque montant
    public $montantCompte = 0;          // montant total 

    public function __construct() {
    }

    public function setCompteMere(CompteMere $compteMere){
        $this->compteMere = $compteMere;
        $this->montantCompte = 0;
    }
    public function addMontant($montant){
        $this->montantCompte += $montant;
    }
    public function addDetailMontant($detail){
        array_push($this->detailsMontant, $detail);
    }
    public function createDetailMontant($compte, $libelle, $montant){
        return ['comptes' => $compte, 'libelle' => $libelle, 'montants' => $montant];
    }

    public function findMontantByCompteMere(array $tabloMontantMouvement, array $tabloCompteMere, CompteMereRepository $mereRepository){
        $dataEtatFinancier = [];
        foreach ($tabloCompteMere as $cptmere) {
            $isMainCptMere = $mereRepository->isMainCptMere($cptmere);
            if($isMainCptMere){                                                                         // Si c'est une compte mere principale
                $etatTemp = new EtatFinancierService();
                $etatTemp->setCompteMere($cptmere);
                $montant = 0;
                $montant = $this->searchCompteInTablo($cptmere,$tabloMontantMouvement);                 // vérifier d'abord le compte_mère
                if($montant > 0){
                    $etatTemp->addMontant($montant);
                    $etatTemp->addDetailMontant($this->createDetailMontant($cptmere->getCptNumero(),$cptmere->getCptLibelle(), $montant));// ajout du detail pour le compte MERE 
                }
                else if($montant == 0){
                    foreach($cptmere->getPlanComptes() as $enfantsMere){                                // boucler les enfants
                        $montant = $this->searchCompteInTablo($enfantsMere,$tabloMontantMouvement);     // comparaison de tableau
                        $etatTemp->addMontant($montant);                                                // ajout du montant total
                        $etatTemp->addDetailMontant($this->createDetailMontant($enfantsMere->getCptNumero(), $enfantsMere->getCptLibelle(), $montant));    // ajout du detail pour compte ENF
                    }
                }
                dump($etatTemp->detailsMontant);
                array_push($dataEtatFinancier, $etatTemp);                                              // Ajout de létat dans le tablo etat
            }
        }
        return $dataEtatFinancier;
    }
    public function searchCompteInTablo($cptEnfants, array $tablo){
        foreach($tablo as $detailMontant){
            if($detailMontant["cpt_numero"] == $cptEnfants->getCptNumero()){
                return $detailMontant["total_montant"];
            }
        }return 0;
    }

}