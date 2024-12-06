<?php

namespace App\Repository;

use App\Entity\Versements;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Versements>
 */
class VersementsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Versements::class);
    }

    /**
     * Enregistre un nouveau versement dans la base de données.
     *
     * Cette méthode crée une nouvelle instance de l'entité `Versements` avec les informations fournies,
     * la persiste en base de données à l'aide de l'EntityManager, et retourne l'objet créé.
     *
     * @param EntityManager $em L'EntityManager de Doctrine utilisé pour persister l'entité.
     * @param string $nom_remettant Le nom de la personne effectuant le versement.
     * @param \DateTime $vrsm_date La date du versement.
     * @param string $adresse L'adresse associée au versement.
     * @param float $vrsm_montant Le montant du versement.
     * @param object $demande_obj L'objet représentant une demande liée au versement.
     * @param object $utilisateur_obj L'objet représentant l'utilisateur associé au versement.
     * @param string $vrsm_motif Le motif ou la raison du versement.
     *
     * @return Versements Retourne l'objet `Versements` qui a été créé et persistant.
     *
     * @throws \Doctrine\ORM\ORMException Si une erreur survient lors de la persistance.
     * @throws \Doctrine\ORM\OptimisticLockException Si une erreur survient lors de l’écriture en base.
     *
     * Fonctionnement :
     * - Une nouvelle instance de `Versements` est créée avec les données fournies.
     * - L'entité est persistée et les modifications sont validées en base de données.
     * - La méthode retourne l'objet `Versements` créé pour une utilisation ultérieure.
     */
    public function persistVersement(EntityManager $em,
                                                   $nom_remettant,
                                                   $vrsm_date,
                                                   $adresse,
                                                   $vrsm_montant,
                                                   $demande_obj,
                                                   $utilisateur_obj,
                                                   $vrsm_motif)
    {
        $vrsm = new Versements($nom_remettant, $vrsm_date, $adresse, $vrsm_montant, $demande_obj, $utilisateur_obj, $vrsm_motif);
        $em->persist($vrsm);
        $em->flush();
        return $vrsm;
    }
}
