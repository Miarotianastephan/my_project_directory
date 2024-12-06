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

    /**
     * Ajoute une observation à une demande existante.
     *
     * Cette méthode permet à un observateur d'ajouter une observation pour une demande spécifique.
     * Elle vérifie d'abord la validité des données (référence de la demande, matricule de l'observateur et contenu de l'observation),
     * puis enregistre l'observation dans la base de données si toutes les conditions sont remplies.
     * En cas d'erreur, une réponse d'erreur est retournée.
     *
     * La méthode procède comme suit :
     * - Vérifie si la demande associée à la référence existe et si elle n'a pas de doublon.
     * - Vérifie la validité de l'observation et du matricule de l'observateur.
     * - Si toutes les vérifications sont réussies, l'observation est enregistrée dans la base de données.
     * - Si une erreur survient lors de l'enregistrement, la transaction est annulée et un message d'erreur est renvoyé.
     *
     * @param string $ref_demande La référence de la demande à laquelle l'observation doit être ajoutée.
     * @param string $matricule_observateur Le matricule de l'observateur ajoutant l'observation.
     * @param string $observation Le contenu de l'observation à ajouter.
     *
     * @return JsonResponse Retourne une réponse JSON avec le statut de l'opération.
     *                      Si l'ajout est réussi, retourne un message de succès, sinon un message d'erreur.
     */
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
            // Démarrage de la transaction
            $entityManager->beginTransaction();
            $observation_demande = new ObservationDemande();
            $observation_demande->setMatriculeObservateur($matricule_observateur);
            $observation_demande->setRefDemande($ref_demande);
            $observation_demande->setObservation($observation);
            $observation_demande->setDateObservation(new \DateTime());
            try {
                // Enregistrement de l'observation dans la base de données
                $entityManager->persist($observation_demande);
                $entityManager->flush();
                $entityManager->commit();
                return new JsonResponse([
                    'success' => true,
                    'message' => 'Observation ajouté avec succès'
                ]);
            } catch (\Exception $e) {
                // En cas d'erreur, annulation de la transaction
                $entityManager->rollback();
                return new JsonResponse([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }

        }
    }

    /**
     * Recherche les observations associées à une demande spécifiée par sa référence.
     *
     * Cette méthode permet de récupérer toutes les observations liées à une demande donnée en fonction de sa référence.
     * Elle effectue une recherche dans la base de données pour trouver toutes les entités `ObservationDemande`
     * correspondant à la référence fournie.
     *
     * @param string $ref_demande La référence de la demande pour laquelle les observations doivent être recherchées.
     *
     * @return ObservationDemande[]|null Un tableau d'objets `ObservationDemande` correspondant à la référence fournie,
     *                                   ou `null` si aucune observation n'est trouvée.
     */
    public function findByRefdemande(string $ref_demande): ?array
    {
        return $this->createQueryBuilder('o')
            ->Where('o.ref_demande = :ref')
            ->setParameter('ref', $ref_demande)
            ->getQuery()
            ->getResult();
    }
}
