<?php

namespace App\Repository;

use App\Entity\DemandeType;
use App\Entity\DetailDemandePiece;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<DetailDemandePiece>
 */
class DetailDemandePieceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DetailDemandePiece::class);
    }

    public function ajoutPieceJustificatif(int    $dm_type_id,
                                           int    $demande_user_id,
                                           string $type,
                                           string $newFilename,
                                           float  $montant_reel): JsonResponse
    {
        $entityManager = $this->getEntityManager();
        // Récupération des entités
        $dm_type = $entityManager->find(DemandeType::class, $dm_type_id);
        if (!$dm_type) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Demande de type introuvable'
            ]);
        }
        $user_demande = $entityManager->find(Utilisateur::class, $demande_user_id);
        if (!$user_demande) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Utilisateur associé à la demande introuvable.'
            ]);
        }
        $user_tresorier = $dm_type->getUtilisateur();
        if (!$user_tresorier) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Utilisateur tresorier est introuvable.'
            ]);
        }

        $connection = $entityManager->getConnection();
        $connection->beginTransaction();
        try {
            $detail_dm = new DetailDemandePiece();
            $detail_dm->setDemandeType($dm_type);
            $detail_dm->setDetDmTypeUrl($type);
            $detail_dm->setDetDmPieceUrl($newFilename);
            $script = "INSERT INTO detail_demande_piece (DETAIL_DM_TYPE_ID, DEMANDE_TYPE_ID,DET_DM_PIECE_URL, DET_DM_TYPE_URL, DET_DM_DATE) VALUES (detail_dm_type_seq.NEXTVAL,:dm_type_id,:det_dm_piece_url,:det_dm_type_url,DEFAULT)";

            $statement = $connection->prepare($script);
            $statement->bindValue('dm_type_id', $dm_type->getId());
            $statement->bindValue('det_dm_piece_url', $newFilename);
            $statement->bindValue('det_dm_type_url', $type);

            //MAJ demande_type
            //MAJ demande_type => mère sy fille avec demande réelle

            $dm_type->setDmEtat(50);
            $dm_type->setMontantReel($montant_reel);
            $dm_type->setUtilisateur($user_demande);

            $entityManager->persist($dm_type);

            $statement->executeQuery();
            $connection->commit();
            $entityManager->flush();
            return new JsonResponse([
                'success' => true,
                'message' => 'Ajout de piece justificative réussie.',
                'dm_type' => $dm_type
            ]);
        } catch (\Exception $e) {
            $connection->rollBack();
            $entityManager->flush();
            // Gestion de l'erreur si le fichier ne peut pas être déplacé
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur ' . $e->getMessage()
            ]);
        }
    }


    public function findByDemandeType(DemandeType $dm_type): array
    {
        return $this->createQueryBuilder('d') // 'd' est l'alias pour l'entité DetailDemandePiece
        ->andWhere('d.demande_type = :val') // Utilisez l'alias 'd' et la propriété correcte
        ->setParameter('val', $dm_type) // Définir le paramètre de recherche
        ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return DetailDemandePiece[] Returns an array of DetailDemandePiece objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?DetailDemandePiece
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
