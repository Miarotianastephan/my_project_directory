<?php

namespace App\Service;

use App\Entity\CompteMere;
use App\Entity\PlanCompte;
use App\Repository\CompteMereRepository;
use App\Repository\PlanCompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;

class PlanCompteService
{

    function insertHierarchy(EntityManagerInterface $entityManager, array $data, ?CompteMere $parent = null) {
        if ($parent === null) {
            if ($this->accountExists($entityManager,$data['numero'], CompteMere::class)) {  // Vérifier si le compte mère existe déjà
                return;
            }
            $compteMere = new CompteMere();                                                 // Création de l'entité CompteMere pour la racine
            $compteMere->setCptNumero($data['numero']);
            $compteMere->setCptLibelle($data['libelle']);
            $entityManager->persist($compteMere);
            $entityManager->flush();
            if (empty($data['enfants'])) {                                                  // Si fils vide donc mère = fils
                $planCompte = new PlanCompte();
                $planCompte->setCptNumero($data['numero']);
                $planCompte->setCptLibelle($data['libelle']);
                $planCompte->setCompteMere($compteMere);
                $entityManager->persist($planCompte);
                $entityManager->flush();
            }
            if (!empty($data['enfants'])) {                                                 // Si l'élément a des enfants, appelle insertHierarchie pour chaque enfant
                foreach ($data['enfants'] as $enfant) {
                    $this->insertHierarchy($entityManager, $enfant, $compteMere);
                }
            }
        } 
        else {  
            if ($this->accountExists($entityManager ,$data['numero'], PlanCompte::class)) { // Vérifier si le plan de compte existe déjà
                return;                                                                     // Ne pas insérer de doublon
            }
            $planCompte = new PlanCompte();                                                 //661 Création de l'entité PlanCompte pour les enfants
            $planCompte->setCptNumero($data['numero']);
            $planCompte->setCptLibelle($data['libelle']);
            $planCompte->setCompteMere($parent);//66
            $entityManager->persist($planCompte);
            $entityManager->flush();                                                        // Flush pour enregistrer l'enfant dans la base
            if (!empty($data['enfants'])) {                                                 // Si l'élément a des enfants, on appelle insertHierarchie pour chaque enfant
                $compteMere = new CompteMere();                                             //661 Création de l'entité CompteMere pour la racine
                $compteMere->setCptNumero($data['numero']);
                $compteMere->setCptLibelle($data['libelle']);
                $entityManager->persist($compteMere);
                $entityManager->flush();                                                    // Flush pour obtenir l'ID du compte mère

                foreach ($data['enfants'] as $enfant) {
                    $this->insertHierarchy($entityManager, $enfant, $compteMere);           // Enfants des enfants
                }
            }
        }
    }

    private function accountExists(EntityManagerInterface $entityManager ,string $numero, string $entityClass): bool
    {
        $repository = $entityManager->getRepository($entityClass);
        $existingAccount = $repository->findOneBy(['cpt_numero' => $numero]);

        return $existingAccount !== null;
    }

    public function updatePlanCompte($plan_cpt_id, $cpt_numero, $cpt_libelle, $cpt_mere_numero, PlanCompteRepository $plCptRepo, CompteMereRepository $cptMere){
        try {
            $plan_to_update = $plCptRepo->find($plan_cpt_id);

            // update
            $stat_num = $plan_to_update->setCptNumero($cpt_numero);
            $stat_lib = $plan_to_update->setCptLibelle($cpt_libelle);
            $stat_mere = false;
    
            // find compte mere by numero
            if($cpt_mere_numero != '-1' && $cpt_libelle != -1){ // Si on change 
               $cpt_mere_update = $cptMere->findByCptNumero($cpt_mere_numero);
               $plan_to_update->setCompteMere($cpt_mere_update);
               $stat_mere = true;
            }
            if($stat_num == false && $stat_lib == false && $stat_mere == false){ // Si aucun changement 
                return [
                    "status" => true,
                    "update" => false,
                    "message" => "Aucun changement effectué !",
                ];
            }

            return $plCptRepo->updatePlanCompte($plan_to_update);
        } catch (InvalidArgumentException $th) { // En cas d'erreur
            return [
                'status' => false,
                'message' => $th->getMessage()
            ];
        }        
    }

    public function hasDataPlanCompte(PlanCompteRepository $plCompteRepo){
        $data_count = $plCompteRepo->count();
        return $retVal = ($data_count>0) ? true : false ;
    }


}