<?php

namespace App\Service;

use App\Entity\CompteMere;
use App\Repository\CompteMereRepository;
use App\Repository\ExerciceRepository;

class EtatFinancierService
{
    public $compteMereNumero;
    public $detailsMontant = [];        // liste d'enfants avec chaque montant
    public $montantCompte = 0;          // montant total
    public $detailMontantTotal = [];

    public function __construct() {
    }

    public function setCompteMere($compteMere){
        $this->compteMereNumero = $compteMere;
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
    public function addDetailMontantTotal($compteNumero, $mere, $libelle ,$montants){ // Ajouter un montant au fur et à mesure
        $this->detailMontantTotal[$compteNumero]['mere'] =  $mere;
        $this->detailMontantTotal[$compteNumero]['libelle'] =  $libelle;
        if(!isset($this->detailMontantTotal[$compteNumero]['montants'])){
            $this->detailMontantTotal[$compteNumero]['montants'] = $montants;
            // dump([ '.Nouveau ajout ' => $this->detailMontantTotal[$compteNumero] ]);
        }
        else{
            // dump([ '       _Avant ajout ' => $this->detailMontantTotal[$compteNumero] ]);
            $this->detailMontantTotal[$compteNumero]['montants'] += $montants;
            // dump([ '       _Apres ajout ' => $this->detailMontantTotal[$compteNumero] ]);
        }
    }

    public function findMontantByCompteMere(array $tabloMontantMouvement, array $tabloCompteMere, CompteMereRepository $mereRepository){
        $dataEtatFinancier = [];
        foreach ($tabloCompteMere as $cptmere) {
            $isMainCptMere = $mereRepository->isMainCptMere($cptmere);
            if($isMainCptMere){                                                                         // Si c'est une compte mere principale
                $etatTemp = new EtatFinancierService();
                $etatTemp->setCompteMere($cptmere->getCptNumero());
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

    public function createEtatFinancierByCompteMere(EtatFinancierService $etatEncours = null, CompteMere $cptmere, array $tabloMontantMouvement, CompteMereRepository $mereRepository){
        if($etatEncours == null){
            $etatEncours = new EtatFinancierService();
            $etatEncours->setCompteMere($cptmere->getCptNumero());
            $montant = 0;
            $montant = $this->searchCompteInTablo($cptmere,$tabloMontantMouvement);                 // vérifier d'abord le compte_mère
            if($montant > 0){
                $etatEncours->addMontant($montant);
                $etatEncours->addDetailMontant($this->createDetailMontant($cptmere->getCptNumero(),$cptmere->getCptLibelle(), $montant));// ajout du detail pour le compte MERE 
            }
            else if($montant == 0){
                foreach($cptmere->getPlanComptes() as $enfantsMere){                                // boucler les enfants
                    $montant = $this->searchCompteInTablo($enfantsMere,$tabloMontantMouvement);     // comparaison chaque enfants sur chaque data
                    $etatEncours->addMontant($montant);
                    $etatEncours->addDetailMontant($this->createDetailMontant($enfantsMere->getCptNumero(), $enfantsMere->getCptLibelle(), $montant));    // ajout du detail pour compte ENF
                    // Jerene ao anaty mère aloha Find mère by ID 
                    $ctempCompteMere = $mereRepository->findByCptNumero($enfantsMere->getCptNumero());
                    if( ($ctempCompteMere != null) && (!empty($ctempCompteMere->getPlanComptes())) ){
                        // manao RECURSSIVE FONCTION 
                        $etatEncours->createEtatFinancierByCompteMere($etatEncours, $ctempCompteMere, $tabloMontantMouvement,$mereRepository);
                    }
                }
            }
        }else{
            foreach($cptmere->getPlanComptes() as $enfantsMere){                                // boucler les enfants
                $montant = $this->searchCompteInTablo($enfantsMere,$tabloMontantMouvement);     // comparaison chaque enfants sur chaque data
                $etatEncours->addMontant($montant);         
                $etatEncours->addDetailMontantTotal($cptmere->getCptNumero(), $cptmere->getCptNumero(), $enfantsMere->getCptLibelle(), $montant);    // ajout du detail pour compte ENF
            }
        }
        
        return $etatEncours;                                                                       // Etat financier
    }

    public function findMontantByCompteMere2(array $tabloMontantMouvement, array $tabloCompteMere, CompteMereRepository $mereRepository){
        $dataEtatFinancier = [];
        foreach ($tabloCompteMere as $cptmere) {
            $isMainCptMere = $mereRepository->isMainCptMere($cptmere);
            if($isMainCptMere){                 
                $etatTemp = $this->createEtatFinancierByCompteMere(null,$cptmere, $tabloMontantMouvement, $mereRepository);
                // dump($etatTemp);
                array_push($dataEtatFinancier, $etatTemp);                                          // Ajout de létat dans le tablo etat
            }
        }
        return $dataEtatFinancier;
    }


}