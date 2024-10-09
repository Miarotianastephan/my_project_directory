<?php

namespace App\Repository;

use App\Entity\CompteMere;
use App\Entity\PlanCompte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CompteMere>
 */
class CompteMereRepository extends ServiceEntityRepository
{
    private PlanCompteRepository $planCompteRepository;
    public static $listCompteDep = ['60','61', '62', '63', '64', '65', '66', '67', '68'];
    public static $listCompteDepPrefixe = ['4%','51%','68%', '7%'];

    public function __construct(ManagerRegistry $registry, PlanCompteRepository $planCptRepo)
    {
        parent::__construct($registry, CompteMere::class);
        $this->planCompteRepository = $planCptRepo;
    }

    public function findByCompteBudget(): ?array
    {


        $queryBuilder = $this->createQueryBuilder('p');

        // Utilisation de 'IN' pour la liste des comptes avec un numéro exact
        $queryBuilder
            ->where($queryBuilder->expr()->in('p.cpt_numero', ':listCompteDep'))
            ->setParameter('listCompteDep',$this->listCompteDep);

        // Construction des clauses 'LIKE' pour chaque préfixe
        $orX = $queryBuilder->expr()->orX(); // Pour gérer plusieurs conditions OR

        foreach ($this->listCompteDepPrefixe as $index => $prefix) {
            $paramName = 'prefix' . $index;
            $orX->add($queryBuilder->expr()->like('p.cpt_numero', ':' . $paramName));
            $queryBuilder->setParameter($paramName, $prefix);
        }

        // Ajout de la condition OR dans la requête
        $queryBuilder->orWhere($orX);

        // Exécution de la requête
        return $queryBuilder->getQuery()->getResult();
    }



    //    /**
    //     * @return CompteMere[] Returns an array of CompteMere objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function findByCptNumero($compteNumero): ?CompteMere
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.cpt_numero = :val')
            ->setParameter('val', $compteNumero)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByPlanCompte(PlanCompte $planCompte): ?CompteMere
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.planComptes = :val')
            ->setParameter('val', $planCompte)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function isMainCptMere(CompteMere $cptMere)
    {
        $temp_numero_compte = $cptMere->getCptNumero();
        $temp_plan_compte = $this->planCompteRepository->findByNumero($temp_numero_compte);
        // Vérifier si le compte mère est principale
        if ($temp_plan_compte == null) {
            // dump(['mere_num' => $temp_numero_compte,'enfant_num' => 'NON-ENFANT']);
            return true;
        }
        if (($temp_plan_compte != null) && ($temp_numero_compte == $temp_plan_compte->getCompteMere()->getCptNumero())) {
            // dump(['mere_num' => $temp_numero_compte,'enfant_num' => $temp_plan_compte->getCptNumero()]);
            return true;
        }
        return false;
    }

    public function findAllChargeCompte()
    {
    }
}
