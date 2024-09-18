<?php

namespace App\Repository;

use App\Entity\DemandeType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findByEtat(int $etat): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.dm_etat = :etat')
            ->setParameter('etat', $etat)
            ->getQuery()
            ->getResult();
    }
    
    public function insertDemandeType(DemandeType $demandeType){
        try {
            $em = $this->getEntityManager();
            $em->persist($demandeType);
            $em->flush();
            return [
                "status" => true,
                "message" => 'Demande insérer avec succes',
            ];
        } catch (\Throwable $th) {
            return [
                "status" => false,
                "message" => $th->getMessage(),
            ];
        }
    }
}
