<?php

namespace App\Repository;

use App\Entity\Banque;
use App\Entity\Chequier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chequier>
 */
class ChequierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chequier::class);
    }

    /**
     * Vérifie si un numéro de chèque existe dans la plage des numéros d'un chéquier pour une banque donnée.
     *
     * Cette méthode permet de vérifier si un numéro de chèque donné se trouve dans une plage de numéros de chèque
     * (entre le numéro de début et le numéro de fin) pour une banque spécifique. Elle renvoie un booléen indiquant
     * si un tel numéro existe déjà dans la base de données.
     *
     * @param string $numero Le numéro de chèque à vérifier.
     * @param Banque $banque L'objet `Banque` représentant la banque à laquelle appartient le chéquier.
     *
     * @return bool 'true' si le numéro de chèque existe dans la plage des numéros du chéquier pour la banque spécifiée,
     *              sinon 'false'.
     */
    public function isExiste(string $numero, Banque $banque): bool
    {
        $count = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.chequier_numero_debut <= :numero')
            ->andWhere('c.chequier_numero_fin >= :numero')
            ->andWhere('c.banque = :banque')
            ->setParameter('numero', $numero, \Doctrine\DBAL\Types\Types::STRING)
            ->setParameter('banque', $banque)
            ->getQuery()
            ->getSingleScalarResult();

        // Retourne true si au moins un résultat existe
        return $count !== null && $count > 0;
    }
}
