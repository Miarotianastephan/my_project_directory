<?php

namespace App\Repository;

use App\Entity\DemandeType;
use App\Entity\LogDemandeType;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<LogDemandeType>
 */
class LogDemandeTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogDemandeType::class);
    }

    public function ajoutValidationDemande(int $dm_type_id, int $sg_user_id): JsonResponse
    {
        $entityManager = $this->getEntityManager();

        try {
            // Récupération des entités
            $dm_type = $entityManager->find(DemandeType::class, $dm_type_id);
            if (!$dm_type) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Demande de type introuvable'
                ]);
            }

            $user_sg = $entityManager->find(Utilisateur::class, $sg_user_id);
            if (!$user_sg) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Utilisateur SG introuvable.'
                ]);
            }

            $user_demande = $dm_type->getUtilisateur();
            if (!$user_demande) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Utilisateur associé à la demande introuvable.'
                ]);
            }

            // Création du log
            $log_dm = new LogDemandeType();
            $log_dm->setDmEtat($dm_type->getDmEtat());
            $log_dm->setUserMatricule($user_demande->getUserMatricule());
            $log_dm->setDemandeType($dm_type);

            // Script SQL pour l'insertion
            $script = "INSERT INTO log_demande_type (LOG_DM_ID, DEMANDE_TYPE_ID, LOG_DM_DATE, DM_ETAT, USER_MATRICULE) 
                   VALUES (log_etat_demande_seq.NEXTVAL, :dm_type_id, DEFAULT, :etat, :user_matricule)";

            $connection = $entityManager->getConnection();
            $connection->beginTransaction();
            try {
                // Préparation et exécution de la requête SQL
                $statement = $connection->prepare($script);
                $statement->bindValue('dm_type_id', $log_dm->getDemandeType()->getId());
                $statement->bindValue('etat', $log_dm->getDmEtat());
                $statement->bindValue('user_matricule', $log_dm->getUserMatricule());
                $statement->executeQuery();

                // Validation de la transaction
                $connection->commit();

                // Mise à jour de l'entité `DemandeType`
                $dm_type->setDmEtat(30);
                $dm_type->setUtilisateur($user_sg);

                $entityManager->flush();
            } catch (\Exception $e) {
                // En cas d'erreur, rollback de la transaction
                $connection->rollBack();
                throw new \Exception('Erreur lors de l\'insertion du log : ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            // Gestion de l'exception générale et retour d'une réponse JSON d'erreur
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la validation de la demande : ' . $e->getMessage()
            ]);
        }

        // Si tout se passe bien, retour d'une réponse JSON de succès
        return new JsonResponse([
            'success' => true,
            'message' => 'La demande a été validée',

        ]);
    }

    public function ajoutRefuserDemande(int $dm_type_id, int $sg_user_id, string $commentaire_data): JsonResponse
    {
        $entityManager = $this->getEntityManager();

        try {
            // Récupération des entités
            $dm_type = $entityManager->find(DemandeType::class, $dm_type_id);
            if (!$dm_type) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Demande de type introuvable'
                ]);
            }
            $user_sg = $entityManager->find(Utilisateur::class, $sg_user_id);
            if (!$user_sg) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Utilisateur SG introuvable.'
                ]);
            }
            $user_demande = $dm_type->getUtilisateur();
            if (!$user_demande) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Utilisateur associé à la demande introuvable.'
                ]);
            }
            if ($commentaire_data === null) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Le message ne peut pas être vide.'
                ]);
            }

            // Création du log
            $log_dm = new LogDemandeType();
            $log_dm->setDmEtat($dm_type->getDmEtat());
            $log_dm->setUserMatricule($user_demande->getUserMatricule());
            $log_dm->setLogDmObservation($commentaire_data);
            $log_dm->setDemandeType($dm_type);

            // Script SQL pour l'insertion
            $script = "INSERT INTO log_demande_type (LOG_DM_ID, DEMANDE_TYPE_ID, LOG_DM_DATE, DM_ETAT, LOG_DM_OBSERVATION, USER_MATRICULE) 
                   VALUES (log_etat_demande_seq.NEXTVAL, :dm_type_id, DEFAULT, :etat, :observation, :user_matricule)";


            $connection = $entityManager->getConnection();
            $connection->beginTransaction();

            try {

                // Préparation et exécution de la requête SQL
                $statement = $connection->prepare($script);
                $statement->bindValue('dm_type_id', $log_dm->getDemandeType()->getId());
                $statement->bindValue('observation', $log_dm->getLogDmObservation());
                $statement->bindValue('etat', $log_dm->getDmEtat());
                $statement->bindValue('user_matricule', $log_dm->getUserMatricule());
                $statement->executeQuery();
                $connection->commit();

                // MAJ de dm_type la base de données
                $dm_type->setDmEtat(20);
                $dm_type->setUtilisateur($user_sg);
                $entityManager->persist($dm_type);

                $entityManager->flush();

            } catch (\Exception $e) {
                // En cas d'erreur, rollback de la transaction
                $connection->rollBack();
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Erreur lors de l\'insertion du log : ' . $e->getMessage()
                ]);
            }

        } catch (\Exception $e) {
            // Gestion de l'exception et retour d'une réponse JSON d'erreur
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors du refus de la demande : ' . $e->getMessage()
            ]);
        }
        // Retour d'une réponse JSON de succès
        return new JsonResponse([
            'success' => true,
            'message' => 'La demande a été refusée avec succès.'

        ]);
    }

    public function ajoutDeblockageFond(int $dm_type_id, int $tresorier_user_id): JsonResponse
    {
        $entityManager = $this->getEntityManager();
        $dm_type = $entityManager->find(DemandeType::class, $dm_type_id);
        if (!$dm_type) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Demande de type introuvable'
            ]);
        }
        $user_tresorier = $entityManager->find(Utilisateur::class, $tresorier_user_id);
        if (!$user_tresorier) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Utilisateur tresorier introuvable.'
            ]);
        }
        $user_sg = $dm_type->getUtilisateur();
        if (!$user_sg) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Utilisateur SG introuvable.'
            ]);
        }

        $log_dm = new LogDemandeType();
        $log_dm->setDmEtat($dm_type->getDmEtat());
        $log_dm->setUserMatricule($user_sg->getUserMatricule());
        $log_dm->setDemandeType($dm_type);

        $script = "INSERT INTO log_demande_type (LOG_DM_ID, DEMANDE_TYPE_ID, LOG_DM_DATE, DM_ETAT, USER_MATRICULE) VALUES (log_etat_demande_seq.NEXTVAL,:dm_type_id,DEFAULT,:etat,:user_matricule)";

        try {
            $connection = $entityManager->getConnection();
            $connection->beginTransaction();
            $statement = $connection->prepare($script);
            $statement->bindValue('dm_type_id', $log_dm->getDemandeType()->getId());
            $statement->bindValue('etat', $log_dm->getDmEtat());
            $statement->bindValue('user_matricule', $log_dm->getUserMatricule());

            $statement->executeQuery();
            $connection->commit();

            // MAJ de dm_type la base de données
            $dm_type->setDmEtat(40);
            $dm_type->setUtilisateur($user_tresorier);
            $entityManager->persist($dm_type);
            $entityManager->flush();
        } catch (\Exception $e) {
            $connection->rollBack();
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors du deblockage de fond : ' . $e->getMessage()
            ]);
        }


        return new JsonResponse([
            'success' => true,
            'message' => 'Le fond a été remis'
        ]);
    }

    //    /**
    //     * @return LogDemandeType[] Returns an array of LogDemandeType objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?LogDemandeType
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
