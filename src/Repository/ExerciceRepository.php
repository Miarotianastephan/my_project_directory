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

    /**
     * Ajoute un nouvel exercice avec une date de début et une date de fin optionnelle.
     *
     * Cette méthode permet de créer et d'ajouter un nouvel exercice à la base de données. L'exercice est initialisé avec
     * une date de début (`date_debut`), et une date de fin (`date_fin`) peut être spécifiée en option. Si la date de fin
     * est fournie, elle est validée avant d'être assignée à l'exercice. En cas d'erreur de format de date ou d'une autre
     * exception, la méthode renvoie un message d'erreur.
     *
     * @param string $date_debut La date de début de l'exercice, au format `YYYY-MM-DD`.
     * @param string|null $date_fin La date de fin de l'exercice, au format `YYYY-MM-DD`. Cette valeur est optionnelle.
     *
     * @return JsonResponse Une réponse JSON contenant un message indiquant si l'ajout a réussi ou échoué.
     */
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
                    // Si la date de fin n'est pas valide, retourne un message d'erreur
                    return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
                }
            }

            // Enregistrer l'exercice (vous devrez probablement appeler l'EntityManager ici)
            $entityManager->persist($exercice);
            $entityManager->flush();

            // Retourne un message JSON indiquant que l'ajout a été effectué avec succès
            return new JsonResponse(['success' => true, 'message' => "L'exercice a été ajouté avec succès."]);

        } catch (\Exception $e) {
            // Si une exception est levée pendant l'opération, effectue un rollback et renvoie un message d'erreur
            $entityManager->rollback();
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Récupère les exercices dont la date de début est après une date donnée et qui n'ont pas de date de fin définie.
     *
     * Cette méthode permet de récupérer une liste d'exercices dans lesquels la date de début est postérieure à une date
     * donnée (`$date`) et qui n'ont pas encore de date de fin définie. Ces exercices peuvent représenter des périodes
     * futures sans date de fin déterminée, comme un exercice en cours ou en attente de fin.
     *
     * @param \DateTime $date La date à partir de laquelle les exercices seront récupérés (doit être une instance de `DateTime`).
     *
     * @return array Un tableau d'exercices correspondant aux critères spécifiés (date de début après `$date` et sans date de fin),
     *               ou un tableau vide si aucun exercice ne correspond.
     */
    public function getExerciceNext(DateTime $date): array
    {
        return $this->createQueryBuilder('e')->where('e.exercice_date_debut > :date')->andWhere('e.exercice_date_fin IS NULL')->setParameter('date', $date, 'customdate')->getQuery()->getResult();
    }

    /**
     * Clôture un exercice donné en lui attribuant une date de fin et en marquant l'exercice comme non valide.
     *
     * Cette méthode permet de clôturer un exercice spécifique en lui assignant une **date de fin** (`$date_cloture`) et en
     * mettant à jour l'état de l'exercice pour le marquer comme non valide. Si l'exercice est déjà clôturé ou s'il n'est
     * pas trouvé, un message d'erreur est retourné. La clôture de l'exercice est effectuée dans une transaction pour assurer
     * l'intégrité des données.
     *
     * @param int $id_exercice L'identifiant de l'exercice à clôturer.
     * @param string $date_cloture La date de clôture de l'exercice au format `YYYY-MM-DD`.
     *
     * @return JsonResponse Une réponse JSON contenant un message indiquant si l'opération de clôture a réussi ou échoué.
     */
    public function cloturerExercice(int $id_exercie, string $date_cloture): JsonResponse
    {
        // Récupère l'exercice valide (en cours)
        $exercice_valide = $this->getExerciceValide();
        $entityManager = $this->getEntityManager();

        // Si aucun exercice valide n'est trouvé, retourne une erreur
        if (!$exercice_valide) {
            return new JsonResponse(['success' => false, 'message' => 'Aucun exercice ouvert']);
        }

        // Récupère l'exercice à clôturer en fonction de son ID
        $exercice = $this->find($id_exercie);
        if (!$exercice || $exercice != $exercice_valide) {
            return new JsonResponse(['success' => false, 'message' => 'Exercice invalide']);
        }

        // Commence une transaction pour garantir que les changements sont appliqués de manière atomique
        $entityManager->beginTransaction();
        try {

            // Mettre à jour la date de fin et l'état de l'exercice
            $date_fin = new \DateTimeImmutable($date_cloture);
            $exercice->setExerciceDateFin($date_fin);
            $exercice->setValid(false);


            $entityManager->persist($exercice);
            $entityManager->flush();

            // Commit transaction
            $entityManager->commit();

            // Retourne un message JSON de succès
            return new JsonResponse(['success' => true, 'message' => "L'exercice cloturer."]);
        } catch (\Exception $e) {

            $entityManager->rollback();
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Récupère l'exercice valide.
     *
     * Cette méthode permet de récupérer un seul exercice qui est marqué comme valide dans la base de données.
     * Elle effectue une requête sur l'entité `Exercice`, en filtrant les exercices où le champ `is_valid` est défini sur `true`.
     * Si un tel exercice existe, il est retourné. Si aucun exercice valide n'est trouvé, la méthode retourne `null`.
     *
     * @return Exercice|null L'exercice valide trouvé, ou null si aucun exercice valide n'existe.
     */
    public function getExerciceValide(): ?Exercice
    {
        return $this->createQueryBuilder('e')
            ->where('e.is_valid = true')
            ->getQuery()
            ->getOneOrNullResult();

    }

    /**
     * Ouvre un exercice en le validant.
     *
     * Cette méthode permet d'ouvrir un exercice en le marquant comme valide. Avant d'ouvrir un nouvel exercice, elle vérifie s'il existe déjà un exercice valide.
     * Si un exercice valide est trouvé, l'opération échoue avec un message d'erreur. Si l'exercice spécifié n'existe pas, un autre message d'erreur est renvoyé.
     * Si l'exercice existe et peut être ouvert, l'état de l'exercice est mis à jour, une transaction est démarrée pour valider la modification, et l'exercice est marqué comme valide.
     * Si la transaction réussit, un message de succès est retourné. En cas d'erreur, la transaction est annulée et un message d'erreur est retourné.
     *
     * @param int $id_exercie L'identifiant de l'exercice à ouvrir.
     *
     * @return JsonResponse Un objet `JsonResponse` contenant le statut de l'opération et le message associé.
     */
    public function ouvertureExercice(int $id_exercie): JsonResponse
    {
        // Vérifie s'il existe déjà un exercice valide
        $exercice_valide = $this->getExerciceValide();
        $entityManager = $this->getEntityManager();

        // Si un exercice valide existe, on retourne une erreur
        if ($exercice_valide) {
            return new JsonResponse(['success' => false, 'message' => 'Fermez tous les exercice']);
        }

        // Recherche l'exercice spécifié par son identifiant
        $exercice = $this->find($id_exercie);
        if (!$exercice) {
            return new JsonResponse(['success' => false, 'message' => 'Exercice invalide']);
        }

        // Commencer une transaction pour effectuer la mise à jour de l'exercice
        $entityManager->beginTransaction();
        try {
            // Mettre à jour la date de fin et l'état de l'exercice
            $exercice->setValid(true);


            $entityManager->persist($exercice);
            $entityManager->flush();

            // Commit transaction
            $entityManager->commit();

            // Retourner une réponse JSON de succès
            return new JsonResponse(['success' => true, 'message' => "L'exercice ouvert."]);
        } catch (\Exception $e) {
            // Si une exception se produit, annuler la transaction
            $entityManager->rollback();

            // Retourner une réponse JSON d'échec avec l'exception
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Récupère l'exercice le plus récent et ouvert.
     *
     * Cette méthode permet de récupérer l'exercice le plus récent dont la date de fin est `NULL`, ce qui signifie qu'il est toujours ouvert.
     * La recherche est effectuée en triant les exercices par la date de début (`exercice_date_debut`) dans l'ordre décroissant, ce qui permet de récupérer l'exercice le plus récent en premier.
     * Seul un exercice est retourné, ou `null` si aucun exercice ouvert n'est trouvé.
     *
     * @return Exercice|null L'exercice ouvert le plus récent, ou `null` si aucun exercice ouvert n'est trouvé.
     */
    public function findMostRecentOpenExercice(): ?Exercice
    {
        return $this->createQueryBuilder('e')->andWhere('e.exercice_date_fin IS NULL')->orderBy('e.exercice_date_debut', 'DESC')->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
}
