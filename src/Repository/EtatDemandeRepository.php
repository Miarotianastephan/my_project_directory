<?php

namespace App\Repository;

use App\Entity\EtatDemande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EtatDemande>
 */
class EtatDemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EtatDemande::class);
    }

    /**
     * Recherche une entité `EtatDemande` en fonction du code d'état.
     *
     * Cette méthode permet de récupérer une entité `EtatDemande` dont le code d'état (`etat_code`) correspond à la valeur
     * spécifiée dans le paramètre `$etatCode`. Si aucun enregistrement ne correspond, la méthode renverra `null`.
     *
     * @param string $etatCode Le code d'état à utiliser pour filtrer les entités `EtatDemande`.
     *
     * @return EtatDemande|null L'entité `EtatDemande` correspondante au code d'état, ou `null` si aucune entité n'est trouvée.
     */
    public function findByEtatCode($etatCode): ?EtatDemande
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.etat_code = :val')
            ->setParameter('val', $etatCode)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
