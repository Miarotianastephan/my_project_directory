<?php

namespace App\Repository;

use App\Entity\Banque;
use App\Entity\CompteMere;
use App\Entity\Demande;
use App\Entity\DemandeType;
use App\Entity\Exercice;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @extends ServiceEntityRepository<DemandeType>
 */
class DemandeTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeType::class);
    }

    public function findByExerciceAndCode(Exercice $exercice,Demande $demande): ?array
    {
        return $this->createQueryBuilder('d')
            ->where('d.exercice = :exercice')
            ->andWhere('d.demande = :demande')
            ->setParameter('exercice', $exercice)
            ->setParameter('demande', $demande)
            ->getQuery()
            ->getResult();
    }



    public function findActiveByExercice(Exercice $exercice, array $les_filtres,Demande $demande): array
    {
        $queryBuilder = $this->createQueryBuilder('d')
            ->where('d.exercice = :exercice')
            ->andWhere('d.demande = :demande')
            ->setParameter('exercice', $exercice)
            ->setParameter('demande', $demande);

        $conditions = [];
        $parameters = new ArrayCollection();

        if ($les_filtres['initie']) {
            $conditions[] = 'd.dm_etat = :etat_initie';
            $parameters->add(['key' => 'etat_initie', 'value' => 100]);
        }
        if ($les_filtres['attente_modif']) {
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

        if (!empty($conditions)) {
            $queryBuilder->andWhere('(' . implode(' OR ', $conditions) . ')');
            // Convert ArrayCollection to parameter array and set them individually
            foreach ($parameters as $param) {
                $queryBuilder->setParameter($param['key'], $param['value']);
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }


    public function findByEtat(Utilisateur $utilisateur = null,array $etats): array
    {
        $queryBuilder = $this->createQueryBuilder('d');
            $queryBuilder->andWhere('d.dm_etat IN (:etats)')
            ->setParameter('etats', $etats);
            if($utilisateur != null){
                $queryBuilder->innerJoin('d.logDemandeTypes', 'log')
                ->andWhere('log.user_matricule = :user_matricule')
                ->setParameter('user_matricule', $utilisateur->getUserMatricule());
            }
        return $queryBuilder->getQuery()->getResult();
    }

    public function findByReference(string $reference): ?array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.ref_demande = :ref')
            ->setParameter('ref', $reference)
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

    public function insertDecaissement(DemandeType $demandeType)
    {
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

    public function findByUtilisateur(Utilisateur $utilisateur)
    {
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
