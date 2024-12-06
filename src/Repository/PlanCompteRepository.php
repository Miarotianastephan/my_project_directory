<?php

namespace App\Repository;

use App\Entity\PlanCompte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlanCompte>
 */
class PlanCompteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanCompte::class);
    }

    /**
     * Recherche un compte par son numéro.
     *
     * Cette méthode permet de récupérer un objet `PlanCompte` en fonction du numéro de compte fourni.
     * Elle effectue une recherche dans la base de données pour trouver l'entité `PlanCompte` correspondant au numéro de compte donné.
     * Si un compte avec ce numéro est trouvé, il est retourné ; sinon, `null` est renvoyé.
     *
     * @param string $cpt_numero Le numéro du compte à rechercher.
     *
     * @return PlanCompte|null Un objet `PlanCompte` correspondant au numéro de compte fourni, ou `null` si aucun compte n'est trouvé.
     */
    public function findByNumero(string $cpt_numero): ?PlanCompte
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.cpt_numero = :val')
            ->setParameter('val', $cpt_numero)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Recherche les comptes de caisse.
     *
     * Cette méthode permet de récupérer tous les comptes dont le numéro commence par "51" et qui sont composés de 6 chiffres.
     * Elle effectue une recherche dans la base de données pour trouver les comptes correspondant à ces critères.
     * Les comptes de caisse sont typiquement associés aux numéros commençant par "51" selon la nomenclature des comptes.
     *
     * @return PlanCompte[] Un tableau d'objets `PlanCompte` correspondant aux comptes de caisse trouvés,
     *                      ou un tableau vide si aucun compte n'est trouvé.
     */
    public function findCompteCaisse(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.cpt_numero LIKE :numStart51')
            ->andWhere('LENGTH(p.cpt_numero) = 6')  // Vérification que le numéro fait bien 6 chiffres
            ->setParameter('numStart51', '51%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche les comptes de dépense.
     *
     * Cette méthode permet de récupérer tous les comptes dont le numéro commence par l'un des préfixes suivants :
     * "61", "62", "63", "64", "65", ou "66", et qui sont composés de 6 chiffres.
     * Elle effectue une recherche dans la base de données pour trouver les comptes correspondant à ces critères.
     * Ces comptes sont typiquement associés aux catégories de dépenses dans le plan comptable.
     *
     * @return PlanCompte[] Un tableau d'objets `PlanCompte` correspondant aux comptes de dépense trouvés,
     *                      ou un tableau vide si aucun compte n'est trouvé.
     */
    public function findCompteDepense(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.cpt_numero LIKE :numStart61')
            ->orWhere('p.cpt_numero LIKE :numStart62')
            ->orWhere('p.cpt_numero LIKE :numStart63')
            ->orWhere('p.cpt_numero LIKE :numStart64')
            ->orWhere('p.cpt_numero LIKE :numStart65')
            ->orWhere('p.cpt_numero LIKE :numStart66')
            ->andWhere('LENGTH(p.cpt_numero) = 6')  // Vérification que le numéro fait bien 6 chiffres
            ->setParameter('numStart61', '61%')
            ->setParameter('numStart62', '62%')
            ->setParameter('numStart63', '63%')
            ->setParameter('numStart64', '64%')
            ->setParameter('numStart65', '65%')
            ->setParameter('numStart66', '66%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche les comptes associés à l'entité "Caisse".
     *
     * Cette méthode permet de récupérer tous les comptes dont le numéro commence par "51" (indiquant généralement un compte de caisse)
     * et qui sont composés de 6 chiffres. Elle effectue une recherche dans la base de données pour trouver les comptes correspondant à ces critères.
     *
     * @return PlanCompte[] Un tableau d'objets `PlanCompte` correspondant aux comptes trouvés,
     *                      ou un tableau vide si aucun compte n'est trouvé.
     */
    public function findEntityCode(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.cpt_numero LIKE :caisse')
            ->andWhere('LENGTH(p.cpt_numero) = 6')
            ->setParameter('caisse', '51%')
            ->getQuery()
            ->getResult();
    }


    /**
     * Met à jour un compte dans le plan comptable.
     *
     * Cette méthode permet de mettre à jour un objet `PlanCompte` en persistante dans la base de données.
     * Si l'opération est réussie, elle retourne un message de succès. En cas d'échec, elle retourne une erreur détaillant la cause de l'échec.
     *
     * Les erreurs peuvent être dues à des violations de contraintes uniques (par exemple, si un numéro de compte déjà existant est tenté d'être mis à jour) ou à d'autres exceptions générales.
     *
     * @param PlanCompte $pl L'objet `PlanCompte` à mettre à jour.
     *
     * @return array Tableau contenant le statut de la mise à jour, un indicateur de succès,
     *               et un message décrivant le résultat de l'opération.
     *               - Si l'update est réussie, le message indique que le compte a été modifié avec succès.
     *               - Si une erreur d'unicité se produit, un message spécifique est renvoyé.
     *               - En cas d'autre exception, le message d'erreur est retourné.
     */
    public function updatePlanCompte(PlanCompte $pl)
    {
        try {
            $em = $this->getEntityManager();
            $em->persist($pl);
            $em->flush();
            return [
                "status" => true,
                "update" => true,
                "message" => sprintf('%s modifié avec succès', $pl->getCptLibelle()),
            ];
        } catch (UniqueConstraintViolationException $exc_unique) {
            // En cas d'erreur
            return $reponse_data = [
                'status' => false,
                'message' => 'Modification non validée, unicité des numéros de comptes !'
            ];
        } catch (\Exception $except) {
            return [
                "status" => false,
                "message" => $except->getMessage(),
            ];
        }
    }

}
