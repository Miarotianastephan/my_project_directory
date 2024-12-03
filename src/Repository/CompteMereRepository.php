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
    public static $listCompteDep = ['60', '61', '62', '63', '64', '65', '66', '67', '68'];
    public static $listCompteDepPrefixe = ['4%', '51%', '68%', '7%'];
    public $listCptDep = ['60', '61', '62', '63', '64', '65', '66', '67', '68'];
    public $listCptDepPrefixe = ['4%', '51%', '68%', '7%'];
    private PlanCompteRepository $planCompteRepository;

    public function __construct(ManagerRegistry $registry, PlanCompteRepository $planCptRepo)
    {
        parent::__construct($registry, CompteMere::class);
        $this->planCompteRepository = $planCptRepo;
    }

    /**
     * @return array|null
     */
    public function findByCompteBudget(): ?array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        // Utilisation de 'IN' pour la liste des comptes avec un numéro exact
        $queryBuilder
            ->where($queryBuilder->expr()->in('p.cpt_numero', ':listCompteDep'))
            ->setParameter('listCompteDep', $this->listCptDep);

        // Construction des clauses 'LIKE' pour chaque préfixe
        $orX = $queryBuilder->expr()->orX(); // Pour gérer plusieurs conditions OR

        foreach ($this->listCptDepPrefixe as $index => $prefix) {
            $paramName = 'prefix' . $index;
            $orX->add($queryBuilder->expr()->like('p.cpt_numero', ':' . $paramName));
            $queryBuilder->setParameter($paramName, $prefix);
        }

        // Ajout de la condition OR dans la requête
        $queryBuilder->orWhere($orX);

        // Exécution de la requête
        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Fonction pour avoir la liste des plans de compte mère pour un plan de compte
     * @param string $compteNumero
     * @return CompteMere|null
     */
    public function findByCptNumero($compteNumero): ?CompteMere
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.cpt_numero = :val')
            ->setParameter('val', $compteNumero)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     *  Fonction pour avoir la liste des plans de compte mère pour un plan de compte
     * @param PlanCompte $planCompte
     * @return CompteMere|null
     */
    public function findByPlanCompte(PlanCompte $planCompte): ?CompteMere
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.planComptes = :val')
            ->setParameter('val', $planCompte)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Fonction récursive pour avoir la source d'un numéro de compte
     * @param CompteMere $cptMere
     * @return bool
     */
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

    /**
     * Pour avoir une liste avec les comptes dont le prefixe est dans le tableau
     * @param array $listComptePre
     * @return mixed
     */
    public function findAllByPrefix(array $listComptePre)
    {
        $queryBuilder = $this->createQueryBuilder('p');
        // Pour gérer plusieurs conditions OR
        $orX = $queryBuilder->expr()->orX();
        foreach ($listComptePre as $index => $prefix) {
            $paramName = 'prefix' . $index;
            $orX->add($queryBuilder->expr()->like('p.cpt_numero', ':' . $paramName));
            $queryBuilder->setParameter($paramName, $prefix);
        }
        // Ajout de la condition OR dans la requête
        $queryBuilder->orWhere($orX);
        // Exécution de la requête
        return $queryBuilder->getQuery()->getResult();
    }
}
