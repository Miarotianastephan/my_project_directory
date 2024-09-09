<?php

namespace App\Repository;

use App\Entity\PlanCompte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<PlanCompte>
 */
class PlanCompteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanCompte::class);
    }

    public function findByNumero(string $cpt_numero): ?PlanCompte
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.cpt_numero = :val')
            ->setParameter('val', $cpt_numero)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return PlanCompte[] Returns an array of PlanCompte objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PlanCompte
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function insertUtilisateur(string $user_matricule, int $groupeId, string $roles): void
    {
        // Requête SQL Oracle
        $sql = "INSERT INTO utilisateur (user_id,user_matricule, grp_id, roles) 
                VALUES (user_seq.NEXTVAL,:user_matricule, :grp_id, :roles)";
        // Récupérer la connexion Doctrine
        $conn = $this->getEntityManager()->getConnection();
        // Démarrer la transaction
        $conn->beginTransaction();
        try {
            // Préparer et exécuter la requête SQL
            $stmt = $conn->prepare($sql);
            $stmt->bindValue('user_matricule', $user_matricule);
            $stmt->bindValue('grp_id', $groupeId);
            $stmt->bindValue('roles', $roles);
            $stmt->executeQuery();
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            throw $e; // Re-throw
        }
    }
}
