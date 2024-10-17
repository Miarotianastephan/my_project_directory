<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    //    /**
    //     * @return Utilisateur[] Returns an array of Utilisateur objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function findOneByUserMatricule($value): ?Utilisateur
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.user_matricule = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findUserByMatricule(string $user_matricule): array{
        $entity_manager = $this->getEntityManager();
        $query = $entity_manager->createQuery(
            'select u.user_matricule
            from APP\Entity\Utilisateur u
            where u.user_matricule = :userMatricule'
        )->setParameter('userMatricule', $user_matricule);
        return $query->getResult();
    }

    public function insertUtilisateur(string $user_matricule, int $groupeId, string $roles): void
    {
        // Requête SQL Oracle
        $sql = "INSERT INTO ce_utilisateur (user_id,user_matricule, grp_id, roles) 
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

    public function updateUtilisateur(Utilisateur $u){
        try {
            $em = $this->getEntityManager();
            $em->persist($u);
            $em->flush();
            return [
                "status" => true,
                "message" => sprintf('Utilisateur %s créer avec succès',$u->getUserMatricule()),
            ];
        } catch (\Exception $except) {
            return [
                "status" => false,
                "message" => $except->getMessage(),
            ];
        }
    }

}
