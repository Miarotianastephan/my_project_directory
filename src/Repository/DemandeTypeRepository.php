<?php

namespace App\Repository;

use App\Entity\Demande;
use App\Entity\DemandeType;
use App\Entity\Exercice;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DemandeType>
 */
class DemandeTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeType::class);
    }

    /**
     * Récupère les demandes de décaissement de fonds pour un exercice et une demande donnés.
     *
     * Cette méthode permet de récupérer toutes les demandes de décaissement de fonds associées à un exercice
     * spécifique et à une demande particulière. Elle effectue une requête DQL pour filtrer les enregistrements
     * en fonction des critères suivants :
     * - L'exercice associé à la demande.
     * - La demande elle-même, en référence à l'objet `Demande` passé en paramètre.
     *
     * @param Exercice $exercice L'exercice pour lequel les demandes de décaissement doivent être récupérées.
     * @param Demande $demande L'objet `Demande` pour lequel les décaissements doivent être recherchés.
     *
     * @return array|null Un tableau contenant les demandes de décaissement de fonds qui correspondent aux critères
     *                    donnés. Si aucune demande ne correspond, la méthode retourne `null` ou un tableau vide.
     */
    public function findByExerciceAndCode(Exercice $exercice, Demande $demande): ?array
    {
        return $this->createQueryBuilder('d')
            ->where('d.exercice = :exercice')
            ->andWhere('d.demande = :demande')
            ->setParameter('exercice', $exercice)
            ->setParameter('demande', $demande)
            ->getQuery()
            ->getResult();
    }


    /**
     * Récupère une liste de demandes actives en fonction de l'exercice, des filtres et du type de demande.
     *
     * Cette méthode permet de récupérer les demandes correspondant à un exercice spécifique et à un type de demande donné
     * (l'objet `Demande`), tout en appliquant des filtres supplémentaires sur l'état des demandes. Les filtres sont passés sous
     * forme d'un tableau associatif, où chaque clé représente un critère d'état spécifique.
     *
     * Les états disponibles pour filtrer sont :
     * - `initie` : Demande avec l'état "initié" (valeur 100).
     * - `attente_modification` : Demande en attente de modification (valeur 201).
     * - `modifier` : Demande ayant été modifiée (valeur 101).
     * - `attente_fond` : Demande en attente de fonds (valeur 200).
     * - `attente_versement` : Demande en attente de versement (valeur 202).
     * - `refuser` : Demande refusée (valeur 301).
     * - `reverser` : Demande à reverser (valeur 401).
     * - `comptabiliser` : Demande comptabilisée (valeur 300).
     * - `justifier` : Demande justifiée (valeur 400).
     *
     * @param Exercice $exercice L'exercice pour lequel les demandes doivent être récupérées.
     * @param array $les_filtres Tableau associatif des filtres à appliquer. Chaque clé correspond à un état spécifique,
     *                           et chaque valeur est un booléen indiquant si le filtre doit être appliqué.
     * @param Demande $demande L'objet `Demande` pour lequel les demandes doivent être récupérées.
     *
     * @return array Liste des demandes actives qui correspondent aux critères donnés. Si aucune demande ne correspond,
     *               un tableau vide sera retourné.
     */
    public function findActiveByExercice(Exercice $exercice, array $les_filtres, Demande $demande): array
    {
        // Création du QueryBuilder pour construire la requête
        $queryBuilder = $this->createQueryBuilder('d')
            ->where('d.exercice = :exercice')
            ->andWhere('d.demande = :demande')
            ->setParameter('exercice', $exercice)
            ->setParameter('demande', $demande);

        // Initialisation des conditions et paramètres
        $conditions = [];
        $parameters = new ArrayCollection();

        // Vérification et ajout des filtres d'état à la requête
        if ($les_filtres['initie']) {
            $conditions[] = 'd.dm_etat = :etat_initie';
            $parameters->add(['key' => 'etat_initie', 'value' => 100]);
        }
        if ($les_filtres['attente_modification']) {
            $conditions[] = 'd.dm_etat = :etat_attente_modif';
            $parameters->add(['key' => 'etat_attente_modif', 'value' => 201]);
        }
        if ($les_filtres['modifier']) {
            $conditions[] = 'd.dm_etat = :etat_modifie';
            $parameters->add(['key' => 'etat_modifie', 'value' => 101]);
        }
        if ($les_filtres['attente_fond']) {
            $conditions[] = 'd.dm_etat = :etat_attente_fond';
            $parameters->add(['key' => 'etat_attente_fond', 'value' => 200]);
        }
        if ($les_filtres['attente_versement']) {
            $conditions[] = 'd.dm_etat = :etat_attente_versement';
            $parameters->add(['key' => 'etat_attente_versement', 'value' => 202]);
        }
        if ($les_filtres['refuser']) {
            $conditions[] = 'd.dm_etat = :etat_refuse';
            $parameters->add(['key' => 'etat_refuse', 'value' => 301]);
        }
        if ($les_filtres['reverser']) {
            $conditions[] = 'd.dm_etat = :etat_reverse';
            $parameters->add(['key' => 'etat_reverse', 'value' => 401]);
        }
        if ($les_filtres['comptabiliser']) {
            $conditions[] = 'd.dm_etat = :etat_comptabilise';
            $parameters->add(['key' => 'etat_comptabilise', 'value' => 300]);
        }
        if ($les_filtres['justifier']) {
            $conditions[] = 'd.dm_etat = :etat_comptabilise';
            $parameters->add(['key' => 'etat_comptabilise', 'value' => 400]);
        }

        // Si des filtres sont définis, ajouter les conditions à la requête
        if (!empty($conditions)) {
            $queryBuilder->andWhere('(' . implode(' OR ', $conditions) . ')');
            // Convert ArrayCollection to parameter array and set them individually
            foreach ($parameters as $param) {
                $queryBuilder->setParameter($param['key'], $param['value']);
            }
        }
        // Exécution de la requête et récupération des résultats
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Récupère une liste de demandes filtrées par état et optionnellement par utilisateur.
     *
     * Cette méthode permet de récupérer les demandes en fonction d'un ou plusieurs états spécifiques.
     * Elle prend également en compte un utilisateur, et si cet utilisateur est fourni, elle restreint la
     * recherche aux demandes qui lui sont associées via une relation de log.
     *
     * @param Utilisateur|null $utilisateur Un objet `Utilisateur` représentant l'utilisateur dont les demandes sont recherchées.
     *                                      Si `null`, toutes les demandes correspondant aux états seront retournées, sans filtre utilisateur.
     * @param array $etats Un tableau des états (`dm_etat`) des demandes à filtrer. Chaque valeur du tableau doit correspondre à un état valide.
     *
     * @return array Un tableau contenant les demandes qui correspondent aux critères donnés.
     *               Si aucune demande ne correspond, la méthode retournera un tableau vide.
     */
    public function findByEtat(Utilisateur $utilisateur = null, array $etats): array
    {
        // Construction du QueryBuilder pour la requête
        $queryBuilder = $this->createQueryBuilder('d');

        // Filtrage des demandes par état (dm_etat)
        $queryBuilder->andWhere('d.dm_etat IN (:etats)')
            ->setParameter('etats', $etats);

        // Si un utilisateur est fourni, filtrage par l'utilisateur associé
        if ($utilisateur != null) {
            $queryBuilder->innerJoin('d.logDemandeTypes', 'log') // Jointure avec la table des logs de demandes
            ->andWhere('log.user_matricule = :user_matricule') // Filtre selon le matricule de l'utilisateur
            ->setParameter('user_matricule', $utilisateur->getUserMatricule());
        }

        // Exécution de la requête et récupération des résultats
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Récupère une liste de demandes filtrées par une référence spécifique.
     *
     * Cette méthode permet de récupérer toutes les demandes ayant une référence spécifique (`ref_demande`).
     * Elle prend en paramètre une chaîne de caractères représentant la référence de la demande à rechercher.
     *
     * @param string $reference La référence de la demande à rechercher. Cette référence est comparée avec le champ `ref_demande` dans la base de données.
     *
     * @return array|null Un tableau contenant les demandes qui correspondent à la référence spécifiée.
     *                    Si aucune demande ne correspond, la méthode retournera `null`.
     */
    public function findByReference(string $reference): ?array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.ref_demande = :ref') // Filtrage des demandes par référence
            ->setParameter('ref', $reference) // Définition de la référence à rechercher
            ->getQuery() // Exécution de la requête
            ->getResult(); // Récupération des résultats
    }

    /**
     * Ajoute une nouvelle demande de décaissement de fonds.
     *
     * Cette méthode permet d'ajouter une nouvelle demande de décaissement de fonds dans la base de données en persistant un objet `DemandeType`.
     * Si l'insertion réussit, un message de succès est retourné. En cas d'erreur, le message d'erreur est retourné.
     *
     * @param DemandeType $demandeType L'objet `DemandeType` à insérer. Il doit contenir toutes les informations nécessaires pour la demande de décaissement.
     *
     * @return array Un tableau contenant deux clés :
     *               - `"status"` (bool) : Un indicateur de réussite ou d'échec de l'opération. `true` si l'insertion a réussi, `false` en cas d'échec.
     *               - `"message"` (string) : Un message de statut indiquant si l'insertion a réussi ou la description de l'erreur.
     */
    public function insertDecaissement(DemandeType $demandeType)
    {
        try {
            // Obtention de l'EntityManager pour interagir avec la base de données
            $em = $this->getEntityManager();

            // Persistance de l'objet DemandeType dans la base de données
            $em->persist($demandeType);

            // Sauvegarde des modifications dans la base de données
            $em->flush();

            // Retourne un tableau de succès
            return [
                "status" => true,
                "message" => 'Demande insérer avec succès',
            ];
        } catch (\Throwable $th) {

            // Gestion des erreurs, retourne un tableau d'échec avec le message d'exception
            return [
                "status" => false,
                "message" => $th->getMessage(),
            ];
        }
    }

    /**
     * Récupère toutes les demandes associées à un utilisateur spécifique.
     *
     * Cette méthode permet de récupérer toutes les demandes (`Demande`) associées à un utilisateur donné.
     * Elle effectue une jointure entre la table des demandes et la table des utilisateurs pour filtrer les demandes par utilisateur.
     *
     * @param Utilisateur $utilisateur L'objet `Utilisateur` pour lequel les demandes doivent être récupérées.
     *
     * @return array Un tableau contenant toutes les demandes qui sont associées à l'utilisateur spécifié.
     *               Si aucune demande n'est trouvée pour cet utilisateur, la méthode retourne un tableau vide.
     */
    public function findByUtilisateur(Utilisateur $utilisateur)
    {
        return $this->createQueryBuilder('d')
            // Jointure avec la table des utilisateurs via la relation 'utilisateur'
            ->innerJoin('d.utilisateur', 'u')
            // Filtrage des demandes où l'utilisateur est égal à l'utilisateur passé en paramètre
            ->andWhere('u = :utilisateur')
            // Définition du paramètre utilisateur dans la requête
            ->setParameter('utilisateur', $utilisateur)
            ->getQuery()
            ->getResult();
    }

    public function findLastInsertedDemandeType(): ?DemandeType
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.id', 'DESC')  // Trie par ID décroissant
            ->setMaxResults(1)         // Limite à un seul résultat
            ->getQuery()
            ->getOneOrNullResult();    // Renvoie null si aucun résultat
    }

    /**
     * Récupère les demandes en attente de validation par le Secrétaire Général.
     *
     * Cette méthode permet de récupérer toutes les demandes dont l'état est
     * soit `initié` (état 100),
     * soit `modifié` (état 101), en attente de validation.
     * Après modification d'une demande, celle-ci doit être validée par le Secrétaire Général.
     * Les demandes dans ces deux états doivent donc être examinées par ce dernier.
     *
     * - **État 100** : état initié, la demande est en cours de création et attend validation.
     * - **État 101** : état modifié, la demande a été modifiée et attend une nouvelle validation.
     *
     * @return array Un tableau contenant toutes les demandes qui sont en état 100 ou 101.
     *               Si aucune demande ne correspond à ces critères, la méthode retourne un tableau vide.
     */
    public function findDemandeAttentes(): array
    {
        return $this->createQueryBuilder('d')
            // Filtrage par état 100 (initié) ou 101 (modifié)
            ->where('d.dm_etat = :etat100')
            ->orwhere('d.dm_etat = :etat101')
            // Définition des paramètres pour les états
            ->setParameter('etat100', 100) // état initié
            ->setParameter('etat101', 101) // état modifié
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère toutes les demandes d'approvisionnement.
     *
     * Cette méthode permet de récupérer toutes les demandes qui sont des approvisionnements.
     * Les demandes ayant un code spécifique (code 20) sont considérées comme des demandes d'approvisionnement.
     * La méthode effectue une jointure avec la table des demandes pour filtrer celles ayant le code `20`,
     * ce qui permet de récupérer uniquement les demandes d'approvisionnement.
     *
     * - **Code 20** : Ce code représente les demandes d'approvisionnement dans le système.
     *
     * @return array Un tableau contenant toutes les demandes d'approvisionnement.
     *               Si aucune demande n'est trouvée, la méthode retourne un tableau vide.
     */
    public function findAllAppro(): array
    {
        // Construction de la requête pour récupérer les demandes d'approvisionnement
        return $this->createQueryBuilder('d')
            // Jointure avec la table des demandes via la relation 'demande'
            ->join('d.demande', 'demande')
            // Filtrage des demandes ayant le code 20 (approvisionnement)
            ->where('demande.dm_code = :codeDemande')
            // Définition du paramètre 'codeDemande' à 20
            ->setParameter('codeDemande', 20)
            ->getQuery()
            ->getResult();
    }

}
