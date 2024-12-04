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

    public function findByNumero(string $cpt_numero): ?PlanCompte
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.cpt_numero = :val')
            ->setParameter('val', $cpt_numero)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCompteCaisse(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.cpt_numero LIKE :numStart51')
            ->andWhere('LENGTH(p.cpt_numero) = 6')  // Vérification que le numéro fait bien 6 chiffres
            ->setParameter('numStart51', '51%')
            ->getQuery()
            ->getResult();
    }

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

    public function findEntityCode(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.cpt_numero LIKE :caisse')
            ->andWhere('LENGTH(p.cpt_numero) = 6')
            ->setParameter('caisse', '51%')
            ->getQuery()
            ->getResult();
    }

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
        } catch (UniqueConstraintViolationException $exc_unique) { // En cas d'erreur
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
