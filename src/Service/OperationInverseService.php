<?php 


namespace App\Service;

use App\Entity\Evenement;
use App\Entity\Mouvement;
use App\Repository\MouvementRepository;
use Doctrine\ORM\EntityManager;

class OperationInverseService{

    // Pour réaliser une opération inverse
    // $mvtRepository: pour persister le nouveau evenement
    /**
     * Réalise l'opération inverse d'une transaction existante.
     *
     * Cette méthode crée des mouvements inverses basés sur la liste des mouvements existants.
     * Si un mouvement est un débit, il crée un mouvement de crédit pour le même compte et vice versa.
     *
     * @param Evenement $evenement L'événement associé à la transaction à inverser.
     * @param EntityManager $em Le gestionnaire d'entités pour persister les nouveaux mouvements.
     * @param array $listMouvement La liste des mouvements à inverser.
     * @param float $montant Le montant de l'opération inverse.
     *
     * @return void
     */
    public function inverseTransaction(Evenement $evenement, EntityManager $em, array $listMouvement, $montant){
        // Parcours de la liste des mouvements existants
        foreach ($listMouvement as $mvtActuelle) {
            if($mvtActuelle->isMvtDebit() == true){
                // Si c'est un mouvement de débit, créer un mouvement de crédit
                $mv_credit = new Mouvement($evenement,$mvtActuelle->getMvtCompteId(),$montant,false);// si DEBIT => crédité
                $em->persist($mv_credit);
            }else if($mvtActuelle->isMvtDebit() == false){
                // Si c'est un mouvement de crédit, créer un mouvement de débit
                $mv_debit = new Mouvement($evenement,$mvtActuelle->getMvtCompteId(),$montant,true);// si CREDIT => débit
                $em->persist($mv_debit);
            }
        }
    }
}