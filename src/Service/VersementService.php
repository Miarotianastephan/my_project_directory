<?php


namespace App\Service;

class VersementService
{

    // Pour générer une référence de versement => VRSM/24/1
    /**
     * Génère une référence pour un versement à partir de son ID.
     *
     * La référence générée suit le format suivant : "VRSM/YYYY/ID",
     * où "YYYY" est l'année en cours et "ID" est l'identifiant du versement passé en paramètre.
     *
     * @param int $Id L'identifiant du versement.
     *
     * @return string La référence du versement au format "VRSM/YYYY/ID".
     */
    public function createReferenceForVersementId($Id)
    {
        return "VRSM/" . date('Y') . "/" . $Id;
    }

}