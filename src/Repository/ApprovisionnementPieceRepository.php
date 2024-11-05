<?php

namespace App\Repository;

use App\Entity\ApprovisionnementPiece;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<ApprovisionnementPiece>
 */
class ApprovisionnementPieceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApprovisionnementPiece::class);
    }
    public function findByRef(string $ref_approvisionnement): ?array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.ref_approvisionnement = :val')
            ->setParameter('val', $ref_approvisionnement)
            ->getQuery()->getResult();
    }
    public function AjoutPiece(string $ref_approvisionnement, string $nomfichier): JsonResponse
    {
        $entityManager = $this->getEntityManager();
        $piece = new ApprovisionnementPiece();
        $piece->setRefApprovisionnement($ref_approvisionnement);
        $piece->setNomFichier($nomfichier);
        $piece->setDateAjout(new \DateTime());

        $connection = $entityManager->getConnection();
        $connection->beginTransaction();
        try {
            $entityManager->persist($piece);
            $entityManager->flush();
            $connection->commit();
            return new JsonResponse([
                'success' => true,
                'message' => 'Ajout de piece justificative réussie.'
            ]);
        } catch (\Exception $e) {
            dump($e->getMessage());
            $connection->rollBack();
            $entityManager->flush();
            // Gestion de l'erreur si le fichier ne peut pas être déplacé
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur ' . $e->getMessage()
            ]);
        }
    }
    //    /**
    //     * @return ApprovisionnementPiece[] Returns an array of ApprovisionnementPiece objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ApprovisionnementPiece
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
