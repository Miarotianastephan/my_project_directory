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
    private $planCompteRepository;
    public function __construct(ManagerRegistry $registry, PlanCompteRepository $planCptRepo)
    {
        parent::__construct($registry, CompteMere::class);
        $this->planCompteRepository = $planCptRepo;
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
               ->getOneOrNullResult()
           ;
       }

       public function findByPlanCompte(PlanCompte $planCompte): ?CompteMere{
            return $this->createQueryBuilder('c')
                ->andWhere('c.planComptes = :val')
                ->setParameter('val', $planCompte)
                ->getQuery()
                ->getOneOrNullResult()
            ;
       }

       public function isMainCptMere(CompteMere $cptMere){
            $temp_numero_compte = $cptMere->getCptNumero();
            $temp_plan_compte = $this->planCompteRepository->findByNumero($temp_numero_compte);
            // Vérifier si le compte mère est principale
            if($temp_plan_compte == null){
                // dump(['mere_num' => $temp_numero_compte,'enfant_num' => 'NON-ENFANT']);
                return true;
            }if( ($temp_plan_compte != null) && ($temp_numero_compte == $temp_plan_compte->getCompteMere()->getCptNumero()) ){
                // dump(['mere_num' => $temp_numero_compte,'enfant_num' => $temp_plan_compte->getCptNumero()]);
                return true;
            }return false;
       }

       public function findAllChargeCompte(){}
}
