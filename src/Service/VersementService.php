<?php 


namespace App\Service;

class VersementService{
    
    // Pour générer un référence de versement => VRSM/24/1
    public function createReferenceForVersementId($Id)
    {
        return "VRSM/" . date('Y') . "/" . $Id;
    }
    
}