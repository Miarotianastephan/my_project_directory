<?php

namespace App\Service;

use App\Entity\CompteMere;
use App\Repository\CompteMereRepository;

class EtatFinancierService
{
    public $compteMereNumero;
    public $compteMereLibelle;
    public $detailsMontant = [];        // liste d'enfants avec chaque montant
    public $montantCompte = 0;          // montant total
    public $detailMontantTotal = [];

    public function __construct()
    {
    }

    public function findMontantByCompteMere2(array $tabloMontantMouvement, array $tabloCompteMere, CompteMereRepository $mereRepository, $isDecaissement = 1)
    {
        $dataEtatFinancier = [];
        foreach ($tabloCompteMere as $cptmere) {
            $isMainCptMere = $mereRepository->isMainCptMere($cptmere);
            if ($isMainCptMere) {
                $etatTemp = $this->createEtatFinancierByCompteMere(null, $cptmere, $tabloMontantMouvement, $mereRepository, $isDecaissement);
                // dump($etatTemp);
                array_push($dataEtatFinancier, $etatTemp);                                          // Ajout de létat dans le tablo etat
            }
        }
        return $dataEtatFinancier;
    }

    public function createEtatFinancierByCompteMere(EtatFinancierService $etatEncours = null, CompteMere $cptmere, array $tabloMontantMouvement, CompteMereRepository $mereRepository, $isDecaissement)
    {
        if ($etatEncours == null) {
            $etatEncours = new EtatFinancierService();
            $etatEncours->setCompteMere($cptmere->getCptNumero());
            $etatEncours->setCompteMereLibelle($cptmere->getCptLibelle());
            $montant = 0;
            $montant = $this->searchCompteInTablo($cptmere, $tabloMontantMouvement, $isDecaissement);                 // vérifier d'abord le compte_mère
            if ($montant > 0) {
                $etatEncours->addMontant($montant);
                $etatEncours->addDetailMontant($this->createDetailMontant($cptmere->getCptNumero(), $cptmere->getCptLibelle(), $montant));// ajout du detail pour le compte MERE
            } else if ($montant == 0) { // Au debut ce sera 0 car compte mere ilay verifiena volohany
                foreach ($cptmere->getPlanComptes() as $enfantsMere) {                                // boucler les enfants
                    $montant = $this->searchCompteInTablo($enfantsMere, $tabloMontantMouvement, $isDecaissement);     // comparaison chaque enfants sur chaque data
                    $etatEncours->addMontant($montant);
                    $etatEncours->addDetailMontant($this->createDetailMontant($enfantsMere->getCptNumero(), $enfantsMere->getCptLibelle(), $montant));    // ajout du detail pour compte ENF
                    // Jerene ao anaty mère aloha Find mère by ID
                    $ctempCompteMere = $mereRepository->findByCptNumero($enfantsMere->getCptNumero());
                    if (($ctempCompteMere != null) && (!empty($ctempCompteMere->getPlanComptes()))) {
                        // manao RECURSSIVE FONCTION
                        $etatEncours->createEtatFinancierByCompteMere($etatEncours, $ctempCompteMere, $tabloMontantMouvement, $mereRepository, $isDecaissement);
                    }
                }
            }
        } else {
            foreach ($cptmere->getPlanComptes() as $enfantsMere) {                                // boucler les enfants
                $montant = $this->searchCompteInTablo($enfantsMere, $tabloMontantMouvement, $isDecaissement);     // comparaison chaque enfants sur chaque data
                $etatEncours->addMontant($montant);
                $etatEncours->addDetailMontantTotal($cptmere->getCptNumero(), $cptmere->getCptNumero(), $cptmere->getCptLibelle(), $montant);    // ajout du detail pour compte ENF
            }
        }

        return $etatEncours;                                                                       // Etat financier
    }

    public function setCompteMere($compteMere)
    {
        $this->compteMereNumero = $compteMere;
        $this->montantCompte = 0;
    }

    public function setCompteMereLibelle($cptLibelle)
    {
        $this->compteMereLibelle = $cptLibelle;
    }

    public function searchCompteInTablo($cptEnfants, array $tablo, $isDecaissement = 1)
    {  // asina argument : compte 7 => (0 : non) ve sa akotrany (1 : oui)
        foreach ($tablo as $detailMontant) {
            if ($detailMontant["cpt_numero"] == $cptEnfants->getCptNumero()) {
                if ($isDecaissement == 1) {
                    return $detailMontant["total_montant"];
                }
                return $detailMontant["total_credit"];
            }
        }
        return 0;
    }

    public function addMontant($montant)
    {
        $this->montantCompte += $montant;
    }

    public function addDetailMontant($detail)
    {
        array_push($this->detailsMontant, $detail);
    }

    public function createDetailMontant($compte, $libelle, $montant)
    {
        return ['comptes' => $compte, 'libelle' => $libelle, 'montants' => $montant];
    }

    public function addDetailMontantTotal($compteNumero, $mere, $libelle, $montants)
    { // Ajouter un montant au fur et à mesure
        $this->detailMontantTotal[$compteNumero]['comptes'] = $mere;
        $this->detailMontantTotal[$compteNumero]['libelle'] = $libelle;
        if (!isset($this->detailMontantTotal[$compteNumero]['montants'])) {
            $this->detailMontantTotal[$compteNumero]['montants'] = $montants;
        } else {
            $this->detailMontantTotal[$compteNumero]['montants'] += $montants;
        }
    }


}