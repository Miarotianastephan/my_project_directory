<?php

namespace App\Repository;

use App\Entity\BudgetType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BudgetType>
 */
class BudgetTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BudgetType::class);
    }

    /**
     * Recherche un seul objet BudgetType basé sur le libellé, en ignorant la casse.
     *
     * Cette méthode utilise une requête DQL pour rechercher un enregistrement dans la base de données
     * dont le champ `libelle` correspond à la valeur passée en paramètre, tout en s'assurant que la
     * comparaison n'est pas sensible à la casse (c'est-à-dire qu'elle ne fait pas de distinction entre
     * majuscules et minuscules).
     *
     * @param string $libelle Le libellé à rechercher dans la base de données.
     *
     * @return BudgetType|null Un objet BudgetType correspondant au libellé fourni si trouvé,
     *                         sinon null si aucun résultat n'est trouvé.
     *
     * @throws \Doctrine\ORM\NonUniqueResultException Si plus d'un résultat est trouvé.
     */
    public function findOneByLibelle(string $libelle): ?BudgetType
    {
        return $this->createQueryBuilder('b')
            ->Where('LOWER(b.libelle) = :val') // pour éviter la sensibilité à la casse
            ->setParameter('val', $libelle)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
