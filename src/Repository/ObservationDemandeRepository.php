<?php

namespace App\Repository;

use App\Entity\ObservationDemande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @extends ServiceEntityRepository<ObservationDemande>
 */
class ObservationDemandeRepository extends ServiceEntityRepository
{
    private DemandeTypeRepository $demandeTypeRepository;

    public function __construct(ManagerRegistry $registry, DemandeTypeRepository $demandeTypeRepos)
    {
        $this->demandeTypeRepository = $demandeTypeRepos;
        parent::__construct($registry, ObservationDemande::class);
    }

    public function ajoutObservation(string $ref_demande, string $matricule_observateur, string $observation): JsonResponse
    {
        $entityManager = $this->getEntityManager();
        $demande_type = $this->demandeTypeRepository->findByReference($ref_demande);
        if (!$demande_type) {
            return new JsonResponse([
                'success' => false,
                'message' => 'La demande n\'éxiste pas'
            ]);
        } else if (count($demande_type) != 1) {
            return new JsonResponse([
                'success' => false,
                'message' => 'La demande existe en plusieurs formats'
            ]);
        } else if (empty(trim($observation))) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Information d\'observation invalide'
            ]);
        } else if (empty(trim($matricule_observateur))) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Connectez vous'

            ]);
        } else {
            $entityManager->beginTransaction();
            $observation_demande = new ObservationDemande();
            $observation_demande->setMatriculeObservateur($matricule_observateur);
            $observation_demande->setRefDemande($ref_demande);
            $observation_demande->setObservation($observation);
            $observation_demande->setDateObservation(new \DateTime());
            try {
                $entityManager->persist($observation_demande);
                $entityManager->flush();
                $entityManager->commit();
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Observation ajouté avec succès'
                ]);
            } catch (\Exception $e) {
                $entityManager->rollback();
                return new JsonResponse([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }

        }
    }

    public function findByRefdemande(string $ref_demande): ?array{
        dump("----->".$ref_demande);
        return $this->createQueryBuilder('o')
            ->Where('o.ref_demande = :ref')
            ->setParameter('ref', $ref_demande)
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return ObservationDemande[] Returns an array of ObservationDemande objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ObservationDemande
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
