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

    /**
     * Recherche des objets basés sur la référence d'approvisionnement.
     *
     * Cette méthode utilise une requête DQL pour rechercher des enregistrements dans la base de données
     * dont le champ `ref_approvisionnement` correspond à la valeur passée en paramètre. Elle renvoie un tableau
     * contenant tous les objets correspondants à la référence d'approvisionnement spécifiée.
     *
     * @param string $ref_approvisionnement La référence d'approvisionnement à rechercher dans la base de données.
     *
     * @return array|null Un tableau d'objets correspondant à la référence d'approvisionnement fournie.
     *               Si aucun enregistrement n'est trouvé, un tableau vide est retourné.
     */
    public function findByRef(string $ref_approvisionnement): ?array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.ref_approvisionnement = :val')
            ->setParameter('val', $ref_approvisionnement)
            ->getQuery()->getResult();
    }

    /**
     * Ajoute une pièce justificative à un approvisionnement.
     *
     * Cette méthode permet d'ajouter une nouvelle pièce justificative à un approvisionnement en
     * associant la référence d'approvisionnement et le nom du fichier à un nouvel objet `ApprovisionnementPiece`.
     * La méthode persiste ensuite l'objet dans la base de données et gère les erreurs de manière appropriée.
     * En cas de succès, un message de confirmation est retourné sous forme de réponse JSON. En cas d'erreur,
     * un message d'erreur est retourné avec les détails de l'exception rencontrée.
     *
     * @param string $ref_approvisionnement La référence de l'approvisionnement auquel la pièce justificative
     *                                     doit être associée.
     * @param string $nomfichier Le nom du fichier de la pièce justificative à ajouter.
     *
     * @return JsonResponse Un objet `JsonResponse` contenant un message de succès ou d'erreur.
     *
     * @throws \Exception Si une erreur se produit lors de l'ajout de la pièce (par exemple, si la
     *                    transaction échoue ou s'il y a un problème avec la base de données).
     */
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
}
