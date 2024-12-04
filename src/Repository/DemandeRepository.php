<?php

namespace App\Repository;

use App\Entity\Demande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Demande>
 */
class DemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Demande::class);
    }

    /**
     * Récupère toutes les demandes et les retourne sous forme de tableau.
     *
     * Cette méthode permet de récupérer toutes les demandes enregistrées dans la base de données,
     * triées par leur identifiant (`id`) de manière croissante (ordre ascendant). Elle utilise
     * Doctrine pour effectuer la requête et retourner un tableau contenant tous les résultats.
     *
     * @return array Un tableau contenant toutes les demandes enregistrées dans la base de données.
     *               Si aucune demande n'est trouvée, un tableau vide sera retourné.
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.id', 'ASC') // Trie par identifiant dans l'ordre croissant
            ->getQuery()
            ->getResult(); // Exécute la requête et récupère tous les résultats
    }

    /**
     * Recherche une demande par son code.
     *
     * Cette méthode permet de récupérer une demande à partir de son code unique. Elle utilise une requête DQL
     * pour rechercher un enregistrement dans la base de données où le champ `dm_code` correspond à la valeur
     * du code passé en paramètre. Si une demande correspond, elle est retournée ; sinon, `null` est retourné.
     *
     * @param string $code_value Le code de la demande à rechercher.
     *
     * @return Demande|null L'objet `Demande` correspondant au code spécifié, ou `null` si aucune demande
     *                      ne correspond.
     */
    public function findDemandeByCode($code_value): ?Demande
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.dm_code = :val')
            ->setParameter('val', $code_value)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
