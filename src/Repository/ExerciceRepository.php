<?php

namespace App\Repository;

use App\Entity\Exercice;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<Exercice>
 */
class ExerciceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercice::class);
    }

    public function ajoutExercice($date_debut, $date_fin = null): JsonResponse
    {
        $entityManager = $this->getEntityManager();
        $exercice = new Exercice();

        try {
            $date_debut = new \DateTimeImmutable($date_debut);
            $exercice->setExerciceDateDebut($date_debut);
            if ($date_fin) {
                try {
                    $date_fin = new \DateTimeImmutable($date_fin);
                    $exercice->setExerciceDateFin($date_fin);
                } catch (\Exception $e) {
                    return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
                }
            }

            // Enregistrer l'exercice (vous devrez probablement appeler l'EntityManager ici)
            $entityManager->persist($exercice);
            $entityManager->flush();
            return new JsonResponse(['success' => true, 'message' => "L'exercice a été ajouté avec succès."]);
        } catch (\Exception $e) {
            $entityManager->rollback();
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getExerciceValide(DateTime $date): array
    {
        return $this->createQueryBuilder('e')->where('e.exercice_date_debut > :date')->andWhere('e.exercice_date_fin IS NULL')->setParameter('date', $date, 'customdate')->getQuery()->getResult();
    }


    public function findMostRecentOpenExercice(): ?Exercice
    {
        return $this->createQueryBuilder('e')->andWhere('e.exercice_date_fin IS NULL')->orderBy('e.exercice_date_debut', 'DESC')->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
}
