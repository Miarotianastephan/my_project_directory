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
     * Liste des demandes de décaissement de fonds à partir du type
     * @param Exercice $exercice
     * @param Demande $demande
     * @return array|null
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
     * Liste de demandes selon les filtres actifs et le type de la demande
     * @param Exercice $exercice
     * @param array $les_filtres
     * @param Demande $demande
     * @return array
     */
    public function findActiveByExercice(Exercice $exercice, array $les_filtres, Demande $demande): array
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

        if (!empty($conditions)) {
            $queryBuilder->andWhere('(' . implode(' OR ', $conditions) . ')');
            // Convert ArrayCollection to parameter array and set them individually
            foreach ($parameters as $param) {
                $queryBuilder->setParameter($param['key'], $param['value']);
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Liste des demandes pour un utilisateur selon les états
     * @param Utilisateur|null $utilisateur
     * @param array $etats
     * @return array
     */
    public function findByEtat(Utilisateur $utilisateur = null, array $etats): array
    {
        $queryBuilder = $this->createQueryBuilder('d');
        $queryBuilder->andWhere('d.dm_etat IN (:etats)')
            ->setParameter('etats', $etats);
        if ($utilisateur != null) {
            $queryBuilder->innerJoin('d.logDemandeTypes', 'log')
                ->andWhere('log.user_matricule = :user_matricule')
                ->setParameter('user_matricule', $utilisateur->getUserMatricule());
        }
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     *
     * @param string $reference
     * @return array|null
     */
    public function findByReference(string $reference): ?array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.ref_demande = :ref')
            ->setParameter('ref', $reference)
            ->getQuery()
            ->getResult();
    }

    /**
     * Ajout décaissement de fonds
     * @param DemandeType $demandeType
     * @return array
     */
    public function insertDecaissement(DemandeType $demandeType)
    {
        try {
            $em = $this->getEntityManager();
            $em->persist($demandeType);
            $em->flush();
            return [
                "status" => true,
                "message" => 'Demande insérer avec succès',
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

    /**
     * Pour avoir les demandes en attentes de validation par le Secrétaire Generale :
     * * état 100 = état initié
     * * état 101 = état modifié -> après modification, une demande doit être validée par le Secrétaire Générale.
     * @return array
     */
    public function findDemandeAttentes(): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.dm_etat = :etat100')
            ->orwhere('d.dm_etat = :etat101')
            ->setParameter('etat100', 100) // état initié
            ->setParameter('etat101', 101) // état modifié
            ->getQuery()
            ->getResult();
    }

    /**
     * Liste des approvisionnements : les demandes avec code 20 sont des approvisionnements.
     * @return array
     */
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
