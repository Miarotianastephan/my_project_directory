<?php

namespace App\Repository;

use App\Entity\Evenement;
use App\Entity\Exercice;
use App\Entity\TransactionType;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evenement>
 */
class EvenementRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    /**
     * Recherche une entité `Evenement` en fonction de la référence d'événement.
     *
     * Cette méthode permet de récupérer une entité `Evenement` dont la référence (`evn_reference`) correspond à la valeur
     * spécifiée dans le paramètre `$reference`. Si aucun enregistrement ne correspond, la méthode renverra `null`.
     *
     * @param string $reference La référence d'événement à utiliser pour filtrer les entités `Evenement`.
     *
     * @return Evenement|null L'entité `Evenement` correspondante à la référence donnée, ou `null` si aucune entité n'est trouvée.
     */
    public function findByEvnReference($reference): ?Evenement
    {
        return $this->createQueryBuilder('e')
            ->where('e.evn_reference = :val')
            ->setParameter('val', $reference)
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * Persiste une nouvelle entité `Evenement` dans la base de données.
     *
     * Cette méthode permet de créer une nouvelle instance de l'entité `Evenement`, de la persister dans la base de données
     * et de la sauvegarder en effectuant un flush. Elle prend plusieurs paramètres qui seront utilisés pour initialiser
     * l'entité `Evenement`.
     *
     * @param EntityManager $em L'EntityManager utilisé pour persister l'entité `Evenement`.
     * @param TransactionType $trsType Le type de la transaction à associer à l'événement.
     * @param Utilisateur $utilisateur L'utilisateur associé à l'événement.
     * @param Exercice $exercice L'exercice comptable auquel l'événement est lié.
     * @param string $codeEntite Le code de l'entité à associer à l'événement.
     * @param float $montant Le montant de la transaction de l'événement.
     * @param string $reference La référence de l'événement.
     * @param \DateTime $dateOperation La date de l'opération pour l'événement.
     *
     * @return Evenement L'entité `Evenement` nouvellement créée et persistée.
     */
    public function persistEvenement(EntityManager   $em,
                                     TransactionType $trsType,
                                     Utilisateur     $utilisateur,
                                     Exercice        $exercice,
                                                     $codeEntite,
                                                     $montant,
                                                     $reference,
                                                     $dateOperation
    )
    {
        // Création d'une nouvelle instance de l'entité Evenement avec les paramètres fournis
        $evn = new Evenement($trsType, $utilisateur, $exercice, $codeEntite, $montant, $reference, $dateOperation);
        $em->persist($evn);
        $em->flush();

        // Retourne l'entité évenement nouvellement persistée
        return $evn;
    }

    // Pour avoir une liste d'Evenement par utilisateur qui l'a effectué
    /**
     * Recherche les événements associés à un responsable donné.
     *
     * Cette méthode permet de récupérer une liste d'entités `Evenement` où le responsable de l'événement
     * correspond à l'utilisateur spécifié. Les événements sont triés par date d'opération dans l'ordre croissant.
     *
     * @param Utilisateur $responsable L'utilisateur (responsable) auquel les événements sont associés.
     *
     * @return Evenement[] Un tableau contenant les événements associés au responsable, triés par date d'opération,
     *                     ou un tableau vide si aucun événement n'est trouvé.
     */
    public function findEvnByResponsable(Utilisateur $responsable)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.evn_responsable = :val')
            ->setParameter('val', $responsable)
            ->orderBy('e.evn_date_operation', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
