<?php

namespace App\Service;

use App\Entity\CompteMere;
use App\Entity\PlanCompte;
use Doctrine\ORM\EntityManagerInterface;

class PlanCompteService
{

    function insertHierarchy(EntityManagerInterface $entityManager, array $data, ?CompteMere $parent = null) {
        if ($parent === null) {
            // Création de l'entité CompteMere pour la racine
            $compteMere = new CompteMere();
            $compteMere->setCptNumero($data['numero']);
            $compteMere->setCptLibelle($data['libelle']);
            $entityManager->persist($compteMere);
            $entityManager->flush(); // Flush pour obtenir l'ID du compte mère
    
            // Si l'élément a des enfants, on appelle la fonction récursivement pour chaque enfant
            if (!empty($data['enfants'])) {
                foreach ($data['enfants'] as $enfant) {
                    $this->insertHierarchy($entityManager, $enfant, $compteMere);
                }
            }
        } else {
            // Création de l'entité PlanCompte pour les enfants
            $planCompte = new PlanCompte();
            $planCompte->setCptNumero($data['numero']);
            $planCompte->setCptLibelle($data['libelle']);
            $planCompte->setCompteMere($parent);
            $entityManager->persist($planCompte);
            $entityManager->flush(); // Flush pour enregistrer l'enfant dans la base
    
            // Si l'élément a des enfants, on appelle la fonction récursivement pour chaque enfant
            if (!empty($data['enfants'])) {
                // Création de l'entité CompteMere pour la racine
                $compteMere = new CompteMere();
                $compteMere->setCptNumero($data['numero']);
                $compteMere->setCptLibelle($data['libelle']);
                $entityManager->persist($compteMere);
                $entityManager->flush(); // Flush pour obtenir l'ID du compte mère
                
                foreach ($data['enfants'] as $enfant) {
                    $this->insertHierarchy($entityManager, $enfant, $parent); // Enfants des enfants
                }
            }
        }
    }


}