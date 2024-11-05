<?php

namespace App\Repository;

use App\Entity\DemandeType;
use App\Entity\DetailDemandePiece;
use App\Entity\LogDemandeType;
use App\Entity\Utilisateur;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<DetailDemandePiece>
 */
class DetailDemandePieceRepository extends ServiceEntityRepository
{

    private $etatDmRepository;

    public function __construct(ManagerRegistry $registry, EtatDemandeRepository $etatDmRepo)
    {
        $this->etatDmRepository = $etatDmRepo;
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
            $script = "INSERT INTO ce_detail_demande_piece (DETAIL_DM_TYPE_ID, DEMANDE_TYPE_ID,DET_DM_PIECE_URL, DET_DM_TYPE_URL, DET_DM_DATE) VALUES (detail_dm_type_seq.NEXTVAL,:dm_type_id,:det_dm_piece_url,:det_dm_type_url,SYSDATE)";

            $statement = $connection->prepare($script);
            $statement->bindValue('dm_type_id', $dm_type->getId());
            $statement->bindValue('det_dm_piece_url', $newFilename);
            $statement->bindValue('det_dm_type_url', $type);

            //MAJ demande_type
            //MAJ demande_type => mère sy fille avec demande réelle

            if ($type == "proformat"){
                $dm_type->setDmEtat($this->etatDmRepository, $dm_type->getDmEtat()); // OK_ETAT : 300 ihany ny 300 eto
            }else{
                // Insérer Validé dans Historique des demandes
                $log_dm = new LogDemandeType();
                $log_dm->setDmEtat($this->etatDmRepository, $dm_type->getDmEtat());                     // HIstorisation du demandes OK_ETAT
                $log_dm->setUserMatricule($user_tresorier->getUserMatricule());
                $log_dm->setDemandeType($dm_type);
                $log_dm->setLogDmDate(new DateTime());
                $entityManager->persist($log_dm);
                // Vérification du montant réel insérer lors de l'ajout du pièce justificatif
                $dm_type->setMontantReel($montant_reel);
                $dm_type->setUtilisateur($user_demande);
                $montant_deblocage = $dm_type->getDmMontant(); 

                if($montant_reel == $montant_deblocage){
                    $dm_type->setDmEtat($this->etatDmRepository, 400);  // de 300 -> 400(Justifié)
                }else if($montant_reel < $montant_deblocage){
                    // Ajoutena anaty Log vaovao hoe nandalo état 400 foana 
                    $log_dm_vrsm = new LogDemandeType();
                    $log_dm_vrsm->setDmEtat($this->etatDmRepository, 400);   // trace état 400(directe dans LOG)
                    $log_dm_vrsm->setUserMatricule($user_tresorier->getUserMatricule());
                    $log_dm_vrsm->setDemandeType($dm_type);
                    $log_dm_vrsm->setLogDmDate(new DateTime());
                    $entityManager->persist($log_dm_vrsm);
                    $entityManager->flush();
                    // Atao état attente de veresement de fonds
                    $dm_type->setDmEtat($this->etatDmRepository, 202);  // de 300 -> 202(Attente)
                    dump("REVERSEMENT EN COURS !!");
                }else if($montant_reel > $montant_deblocage){
                    // Nouveau demande
                    // Updatena ny champ an'ilay demande
                }
            }


            $entityManager->persist($dm_type);

            $statement->executeQuery();
            $entityManager->flush();
            $connection->commit();
            return new JsonResponse([
                'success' => true,
                'message' => 'Ajout de piece justificative réussie.',
                'dm_type' => $dm_type
            ]);
        } catch (\Exception $e) {
            dump($e->getMessage());
            $connection->rollBack();
            // Gestion de l'erreur si le fichier ne peut pas être déplacé
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur ' . $e->getMessage()
            ]);
        }
    }


    public function findByDemandeType(DemandeType $dm_type): ?array
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
