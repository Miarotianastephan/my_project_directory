<?php

namespace App\Entity;

use App\Repository\MouvementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MouvementRepository::class)]
class Mouvement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'mvn_seq')]
    #[ORM\Column(name: 'mvn_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "mvt_evenement_id", referencedColumnName: "evn_id", nullable: false)]
    private ?Evenement $mvt_evenement_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "mvt_compte_id", referencedColumnName: "cpt_id", nullable: false)]
    private ?PlanCompte $mvt_compte_id = null;

    #[ORM\Column]
    private ?float $mvt_montant = null;

    #[ORM\Column]
    private ?bool $isMvtDebit = null;

    public function getMvtEvenementId(): ?Evenement
    {
        return $this->mvt_evenement_id;
    }

    public function setMvtEvenementId(?Evenement $mvt_evenement_id): static
    {
        $this->mvt_evenement_id = $mvt_evenement_id;

        return $this;
    }

    public function getMvtCompteId(): ?PlanCompte
    {
        return $this->mvt_compte_id;
    }

    public function setMvtCompteId(?PlanCompte $mvt_compte_id): static
    {
        $this->mvt_compte_id = $mvt_compte_id;

        return $this;
    }

    public function getMvtMontant(): ?float
    {
        return $this->mvt_montant;
    }

    public function setMvtMontant(float $mvt_montant): static
    {
        $this->mvt_montant = $mvt_montant;

        return $this;
    }

    public function isMvtDebit(): ?bool
    {
        return $this->isMvtDebit;
    }

    public function setMvtDebit(bool $isMvtDebit): static
    {
        $this->isMvtDebit = $isMvtDebit;

        return $this;
    }

    public function __toString(): string
    {
        $evenement = $this->mvt_evenement_id ? $this->mvt_evenement_id->getId() : 'N/A';
        $compte = $this->mvt_compte_id ? $this->mvt_compte_id->getId() : 'N/A';
        $montant = $this->mvt_montant ?? 'N/A';
        $type = $this->isMvtDebit ? 'Débit' : 'Crédit';

        return sprintf(
            'Mouvement [Événement: %s, Compte: %s, Montant: %.2f, Type: %s]',
            $evenement,
            $compte,
            $montant,
            $type
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

}
