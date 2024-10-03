<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'v_demande')]
class VDemande
{
    #[ORM\Id]
    #[ORM\Column(name: 'v_demande_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Exercice::class)]
    #[ORM\JoinColumn(name: 'exercice_id', referencedColumnName: 'exercice_id')]
    private ?Exercice $exercice = null;

    #[ORM\ManyToOne(targetEntity: PlanCompte::class)]
    #[ORM\JoinColumn(name: '$cpt_id', referencedColumnName: 'id')]
    private ?PlanCompte $planCompte = null;

    #[ORM\Column(length: 255)]
    private ?string $mois = null;

    #[ORM\Column]
    private ?float $total_montant = 0;

    /**
     * @return PlanCompte|null
     */
    public function getPlanCompte(): ?PlanCompte
    {
        return $this->planCompte;
    }

    /**
     * @param PlanCompte|null $planCompte
     */
    public function setPlanCompte(?PlanCompte $planCompte): void
    {
        $this->planCompte = $planCompte;
    }

    /**
     * @return Exercice|null
     */
    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    /**
     * @param Exercice|null $exercice
     */
    public function setExercice(?Exercice $exercice): void
    {
        $this->exercice = $exercice;
    }

    /**
     * @return string|null
     */
    public function getMois(): ?string
    {
        return $this->mois;
    }

    /**
     * @param string|null $mois
     */
    public function setMois(?string $mois): void
    {
        $this->mois = $mois;
    }

    /**
     * @return float|null
     */
    public function getTotalMontant(): ?float
    {
        return $this->total_montant;
    }

    /**
     * @param float|null $total_montant
     */
    public function setTotalMontant(?float $total_montant): void
    {
        $this->total_montant = $total_montant;
    }
}