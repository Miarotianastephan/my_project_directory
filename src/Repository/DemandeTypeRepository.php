<?php

namespace App\Repository;

use App\Entity\Banque;
use App\Entity\CompteMere;
use App\Entity\DemandeType;
use App\Entity\PlanCompte;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;


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
    
    public function ajout_approvisionnement(int                $entite_id,
                                            int                $banque_id,
                                            int                $chequier_numero,
                                            float              $montant,
                                            ChequierRepository $chequierRepository): JsonResponse

    {
        $entityManager = $this->getEntityManager();
        $compte_mere = $entityManager->find(CompteMere::class, $entite_id);
        if (!$compte_mere) {
            return new JsonResponse(
                ['success' => false, 'message' => "L'entité choisi n'éxiste pas"]
            );
        }
        $banque = $entityManager->find(Banque::class, $banque_id);
        if (!$banque) {
            return new JsonResponse(
                ['success' => false, 'message' => "La banque associée n'existe pas"]
            );
        }
        $isExiste = $chequierRepository->isExiste($chequier_numero, $banque);
        if (!$isExiste) {
            return new JsonResponse(
                ['success' => false, 'message' => "Le chequier n'existe pas"]
            );
        }
        return new JsonResponse(
            ['success' => true, 'message' => "Insertion réussi", 'isExiste' => false]
        );
    }

    public function insertDecaissement(DemandeType $demandeType){
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

    public function findByUtilisateur(Utilisateur $utilisateur){
        return $this->createQueryBuilder('d')
            ->innerJoin('d.utilisateur', 'u')
            ->andWhere('u = :utilisateur')
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

    // Pour avoir les demandes en atttentes de validation par le Secretaire Generale
    public function findDemandeAttentes(): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.dm_etat = :etat100')
            ->orwhere('d.dm_etat = :etat101')
            ->setParameter('etat100', 100)
            ->setParameter('etat101', 101)
            ->getQuery()
            ->getResult();
    }
    public function findAllAppro(): array
    {
        return $this->createQueryBuilder('d')
            ->join('d.demande', 'demande')
            ->where('demande.dm_code = :codeDemande')
            ->setParameter('codeDemande', 20)
            ->getQuery()
            ->getResult();
    }

}
