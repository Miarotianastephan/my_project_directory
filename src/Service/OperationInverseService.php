<?php 


namespace App\Service;

use App\Entity\Evenement;
use App\Entity\Mouvement;
use App\Repository\MouvementRepository;
use Doctrine\ORM\EntityManager;

class OperationInverseService{
    
    // Pour réaliser une opération inverse
    // $mvtRepository: pour persister le nouveau evenement
    public function inverseTransaction(Evenement $evenement, EntityManager $em, array $listMouvement, $montant){
        foreach ($listMouvement as $mvtActuelle) {
            if($mvtActuelle->isMvtDebit() == true){
                // Creer un mouvement même compte, mais en debit
                $mv_credit = new Mouvement($evenement,$mvtActuelle->getMvtCompteId(),$montant,false);// siDEBIT => crédité
                $em->persist($mv_credit);
            }else if($mvtActuelle->isMvtDebit() == false){
                // créer un mouvement même compte, mais en credit
                $mv_debit = new Mouvement($evenement,$mvtActuelle->getMvtCompteId(),$montant,true);// siCREDIT => débit
                $em->persist($mv_debit);
            }
        }
    }
}