<?php

namespace App\Service;

use App\Entity\CompteMere;
use App\Repository\CompteMereRepository;

/**
 * Service de gestion de l'état financier basé sur les comptes mère et leurs enfants.
 *
 * Cette classe permet de calculer et de générer l'état financier en fonction des mouvements financiers (décaissements ou crédits)
 * associés aux comptes mère et leurs comptes enfants. Elle permet de récupérer les montants associés à chaque compte
 * ainsi que de gérer l'agrégation des montants pour obtenir des informations financières globales.
 */
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

    /**
     * Trouve les montants associés aux comptes mères et leurs enfants, et génère l'état financier.
     *
     * Parcourt tous les comptes mères fournis, et pour chaque compte mère, calcule le montant en fonction des mouvements
     * financiers des comptes associés, en fonction du paramètre `isDecaissement`.
     *
     * @param array $tabloMontantMouvement Tableau contenant les mouvements financiers associés à chaque compte.
     * @param array $tabloCompteMere Tableau contenant les comptes mères à traiter.
     * @param CompteMereRepository $mereRepository Repository pour récupérer les informations des comptes mères.
     * @param int $isDecaissement Indique si les mouvements financiers sont des décaissements (1) ou des crédits (0).
     * @return array Tableau contenant l'état financier des comptes mères.
     */
    public function findMontantByCompteMere2(array $tabloMontantMouvement, array $tabloCompteMere, CompteMereRepository $mereRepository, $isDecaissement = 1)
    {
        $dataEtatFinancier = [];
        foreach ($tabloCompteMere as $cptmere) {
            $isMainCptMere = $mereRepository->isMainCptMere($cptmere);
            if ($isMainCptMere) {
                $etatTemp = $this->createEtatFinancierByCompteMere(null, $cptmere, $tabloMontantMouvement, $mereRepository, $isDecaissement);
                array_push($dataEtatFinancier, $etatTemp); // Ajout de l'état dans le tableau état
            }
        }
        return $dataEtatFinancier;
    }


    /**
     * Crée l'état financier pour un compte mère et ses comptes enfants.
     *
     * Si l'état financier pour le compte mère n'existe pas, il est créé. Les montants des comptes enfants sont ajoutés
     * récursivement à l'état financier. Si un montant pour un enfant n'est pas trouvé, il est initialisé à zéro.
     *
     * @param EtatFinancierService|null $etatEncours État financier en cours (peut être nul lors de la première création).
     * @param CompteMere $cptmere Compte mère pour lequel l'état financier est généré.
     * @param array $tabloMontantMouvement Tableau des mouvements financiers.
     * @param CompteMereRepository $mereRepository Repository pour récupérer les informations des comptes mères.
     * @param int $isDecaissement Indique si les mouvements sont des décaissements (1) ou des crédits (0).
     * @return EtatFinancierService L'état financier mis à jour pour le compte mère.
     */
    public function createEtatFinancierByCompteMere(EtatFinancierService $etatEncours = null, CompteMere $cptmere, array $tabloMontantMouvement, CompteMereRepository $mereRepository, $isDecaissement)
    {
        if ($etatEncours == null) {
            // Création d'un nouvel état financier si aucun état n'est passé
            $etatEncours = new EtatFinancierService();
            $etatEncours->setCompteMere($cptmere->getCptNumero());
            $etatEncours->setCompteMereLibelle($cptmere->getCptLibelle());
            $montant = $this->searchCompteInTablo($cptmere, $tabloMontantMouvement, $isDecaissement); // vérifier d'abord le compte_mère

            if ($montant > 0) {
                // Si un montant est trouvé, on l'ajoute à l'état financier
                $etatEncours->addMontant($montant);
                $etatEncours->addDetailMontant($this->createDetailMontant($cptmere->getCptNumero(), $cptmere->getCptLibelle(), $montant));// ajout du detail pour le compte MERE
            } else if ($montant == 0) {
                // Si aucun montant n'est trouvé pour le compte mère, on vérifie ses enfants
                // Au debut ce sera 0 car compte mere ilay verifiena volohany
                foreach ($cptmere->getPlanComptes() as $enfantsMere) {                                // boucler les enfants
                    $montant = $this->searchCompteInTablo($enfantsMere, $tabloMontantMouvement, $isDecaissement);     // comparaison chaque enfant sur chaque data
                    $etatEncours->addMontant($montant);
                    $etatEncours->addDetailMontant($this->createDetailMontant($enfantsMere->getCptNumero(), $enfantsMere->getCptLibelle(), $montant));    // ajout du detail pour compte ENF

                    // Jerene ao anaty mère aloha Find mère by ID
                    // Appel récursif pour vérifier les comptes enfants
                    $ctempCompteMere = $mereRepository->findByCptNumero($enfantsMere->getCptNumero());
                    if (($ctempCompteMere != null) && (!empty($ctempCompteMere->getPlanComptes()))) {
                        // manao RECURSSIVE FONCTION
                        $etatEncours->createEtatFinancierByCompteMere($etatEncours, $ctempCompteMere, $tabloMontantMouvement, $mereRepository, $isDecaissement);
                    }
                }
            }
        } else {
            // Si l'état financier existe déjà, on ajoute les montants des enfants
            foreach ($cptmere->getPlanComptes() as $enfantsMere) { // boucler les enfants
                $montant = $this->searchCompteInTablo($enfantsMere, $tabloMontantMouvement, $isDecaissement);     // comparaison chaque enfant sur chaque data
                $etatEncours->addMontant($montant);
                $etatEncours->addDetailMontantTotal($cptmere->getCptNumero(), $cptmere->getCptNumero(), $cptmere->getCptLibelle(), $montant);    // ajout du detail pour compte ENF
            }
        }

        return $etatEncours; // Etat financier
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

    /**
     * Recherche un montant dans le tableau des mouvements financiers pour un compte donné.
     *
     * @param CompteMere $cptEnfants Le compte enfant pour lequel rechercher le montant.
     * @param array $tablo Tableau des mouvements financiers.
     * @param int $isDecaissement Indique si la recherche concerne les décaissements (1) ou les crédits (0).
     * @return float Le montant trouvé, ou 0 si aucun montant n'est trouvé.
     */
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

    /**
     * Mettre à jour le montant
     * @param $montant
     * @return void
     */
    public function addMontant($montant)
    {
        $this->montantCompte += $montant;
    }

    /**
     * Mettre à jour le détail de montant aux détails des montants.
     * @param $detail
     * @return void
     */
    public function addDetailMontant($detail)
    {
        array_push($this->detailsMontant, $detail);
    }

    /**
     * Crée un détail de montant pour un compte.
     *
     * @param string $compte Le numéro du compte.
     * @param string $libelle Le libellé du compte.
     * @param float $montant Le montant associé au compte.
     * @return array Le détail du montant sous forme de tableau associatif.
     */
    public function createDetailMontant($compte, $libelle, $montant)
    {
        return ['comptes' => $compte, 'libelle' => $libelle, 'montants' => $montant];
    }

    /**
     * Ajoute un détail de montant total pour un compte mère.
     *
     * @param string $compteNumero Le numéro du compte mère.
     * @param string $mere Le numéro du compte mère.
     * @param string $libelle Le libellé du compte mère.
     * @param float $montants Le montant total associé au compte mère.
     */
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