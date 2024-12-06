<?php

namespace App\Service;

use App\Entity\CompteMere;
use App\Entity\PlanCompte;
use App\Repository\CompteMereRepository;
use App\Repository\PlanCompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class PlanCompteService
{

    /**
     * Insère une hiérarchie de comptes dans la base de données.
     *
     * Cette méthode crée un compte mère et/ou des comptes enfants en fonction des données fournies.
     * Si un compte mère n'existe pas, il sera créé. Si des comptes enfants sont présents, ils sont associés
     * au compte mère et la hiérarchie est insérée récursivement.
     *
     * @param EntityManagerInterface $entityManager L'EntityManager pour persister les entités.
     * @param array $data Les données pour insérer le compte, incluant les enfants (si présents).
     * @param CompteMere|null $parent Le compte mère parent (si applicable).
     *
     * @return void
     */
    function insertHierarchy(EntityManagerInterface $entityManager, array $data, ?CompteMere $parent = null)
    {
        if ($parent === null) {
            if ($this->accountExists($entityManager, $data['numero'], CompteMere::class)) {  // Vérifier si le compte mère existe déjà
                return; // Ne pas insérer un doublon si le compte mère existe déjà
            }
            $compteMere = new CompteMere();                                                 // Création de l'entité CompteMere pour la racine
            $compteMere->setCptNumero($data['numero']);
            $compteMere->setCptLibelle($data['libelle']);
            $entityManager->persist($compteMere);
            $entityManager->flush();
            if (empty($data['enfants'])) {                                                  // Si fils vide donc mère = fils
                // Si le compte mère n'a pas d'enfants, créer un plan de compte pour lui
                $planCompte = new PlanCompte();
                $planCompte->setCptNumero($data['numero']);
                $planCompte->setCptLibelle($data['libelle']);
                $planCompte->setCompteMere($compteMere);
                $entityManager->persist($planCompte);
                $entityManager->flush();
            }
            if (!empty($data['enfants'])) {                                                 // Si l'élément a des enfants, appelle insertHierarchie pour chaque enfant
                // Si des enfants existent, les insérer récursivement
                foreach ($data['enfants'] as $enfant) {
                    $this->insertHierarchy($entityManager, $enfant, $compteMere);
                }
            }
        } else {
            if ($this->accountExists($entityManager, $data['numero'], PlanCompte::class)) { // Vérifier si le plan de compte existe déjà
                return;                                                                     // Ne pas insérer un doublon si le plan de compte existe déjà
            }
            $planCompte = new PlanCompte();                                                 //661 Création de l'entité PlanCompte pour les enfants
            $planCompte->setCptNumero($data['numero']);
            $planCompte->setCptLibelle($data['libelle']);
            $planCompte->setCompteMere($parent);//66
            $entityManager->persist($planCompte);
            $entityManager->flush();                                                        // Flush pour enregistrer l'enfant dans la base
            if (!empty($data['enfants'])) {                                                 // Si l'élément a des enfants, on appelle insertHierarchie pour chaque enfant
                // Si l'élément a des enfants, insérer la hiérarchie pour chaque enfant
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

    /**
     * Vérifie si un compte existe déjà dans la base de données.
     *
     * Cette méthode vérifie si un compte avec le numéro donné existe déjà dans la base de données,
     * pour éviter d'insérer des doublons.
     *
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour interroger la base de données.
     * @param string $numero Le numéro du compte à vérifier.
     * @param string $entityClass La classe de l'entité (soit `CompteMere`, soit `PlanCompte`).
     *
     * @return bool Retourne `true` si le compte existe, `false` sinon.
     */
    private function accountExists(EntityManagerInterface $entityManager, string $numero, string $entityClass): bool
    {
        $repository = $entityManager->getRepository($entityClass);
        $existingAccount = $repository->findOneBy(['cpt_numero' => $numero]);

        return $existingAccount !== null;
    }

    /**
     * Met à jour un plan de compte existant.
     *
     * Cette méthode permet de modifier les informations d'un plan de compte, y compris son numéro,
     * son libellé et son compte mère associé. Si aucun changement n'est détecté, un message indiquant
     * qu'aucune modification n'a été effectuée est retourné.
     *
     * @param int $plan_cpt_id L'ID du plan de compte à mettre à jour.
     * @param string $cpt_numero Le nouveau numéro de compte.
     * @param string $cpt_libelle Le nouveau libellé de compte.
     * @param string $cpt_mere_numero Le numéro du compte mère à associer.
     * @param PlanCompteRepository $plCptRepo Le repository des plans de comptes.
     * @param CompteMereRepository $cptMere Le repository des comptes mères.
     *
     * @return array Un tableau contenant le statut de l'opération (`status`) et un message ou une information
     *               sur la mise à jour effectuée.
     */
    public function updatePlanCompte($plan_cpt_id, $cpt_numero, $cpt_libelle, $cpt_mere_numero, PlanCompteRepository $plCptRepo, CompteMereRepository $cptMere)
    {
        try {
            $plan_to_update = $plCptRepo->find($plan_cpt_id);

            // Mise à jour des attributs du plan de compte
            $stat_num = $plan_to_update->setCptNumero($cpt_numero);
            $stat_lib = $plan_to_update->setCptLibelle($cpt_libelle);
            $stat_mere = false;

            // Recherche du compte mère par numéro
            if ($cpt_mere_numero != '-1' && $cpt_libelle != -1) {
                // Si on change le compte mère
                $cpt_mere_update = $cptMere->findByCptNumero($cpt_mere_numero);
                $plan_to_update->setCompteMere($cpt_mere_update);
                $stat_mere = true;
            }
            if ($stat_num == false && $stat_lib == false && $stat_mere == false) { // Si aucun changement
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

    /**
     * Vérifie si des données de plan de compte existent dans la base de données.
     *
     * Cette méthode permet de savoir si des plans de comptes existent déjà dans la base de données.
     *
     * @param PlanCompteRepository $plCompteRepo Le repository des plans de comptes.
     *
     * @return bool Retourne `true` si des plans de comptes existent, `false` sinon.
     */
    public function hasDataPlanCompte(PlanCompteRepository $plCompteRepo)
    {
        $data_count = $plCompteRepo->count();
        return $retVal = ($data_count > 0) ? true : false;
    }


}