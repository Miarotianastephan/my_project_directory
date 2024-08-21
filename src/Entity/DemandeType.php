<?php

namespace App\Entity;

use App\Repository\DemandeTypeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeTypeRepository::class)]
class DemandeType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dm_date = null;

    #[ORM\Column]
    private ?float $dm_montant = null;

    #[ORM\Column(length: 255)]
    private ?string $entity_code = null;

    #[ORM\Column(length: 255)]
    private ?string $dm_mode_paiement = null;

    #[ORM\Column(length: 255)]
    private ?string $ref_demande = null;

    #[ORM\Column(length: 255)]
    private ?string $dm_etat = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlanCompte $plan_compte = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Exercice $exercice = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Demande $demande = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDmDate(): ?\DateTimeInterface
    {
        return $this->dm_date;
    }

    public function setDmDate(\DateTimeInterface $dm_date): static
    {
        $this->dm_date = $dm_date;

        return $this;
    }

    public function getDmMontant(): ?float
    {
        return $this->dm_montant;
    }

    public function setDmMontant(float $dm_montant): static
    {
        $this->dm_montant = $dm_montant;

        return $this;
    }

    public function getEntityCode(): ?string
    {
        return $this->entity_code;
    }

    public function setEntityCode(string $entity_code): static
    {
        $this->entity_code = $entity_code;

        return $this;
    }

    public function getDmModePaiement(): ?string
    {
        return $this->dm_mode_paiement;
    }

    public function setDmModePaiement(string $dm_mode_paiement): static
    {
        $this->dm_mode_paiement = $dm_mode_paiement;

        return $this;
    }

    public function getRefDemande(): ?string
    {
        return $this->ref_demande;
    }

    public function setRefDemande(string $ref_demande): static
    {
        $this->ref_demande = $ref_demande;

        return $this;
    }

    public function getDmEtat(): ?string
    {
        return $this->dm_etat;
    }

    public function setDmEtat(string $dm_etat): static
    {
        $this->dm_etat = $dm_etat;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getPlanCompte(): ?PlanCompte
    {
        return $this->plan_compte;
    }

    public function setPlanCompte(?PlanCompte $plan_compte): static
    {
        $this->plan_compte = $plan_compte;

        return $this;
    }

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): static
    {
        $this->exercice = $exercice;

        return $this;
    }

    public function getDemande(): ?Demande
    {
        return $this->demande;
    }

    public function setDemande(?Demande $demande): static
    {
        $this->demande = $demande;

        return $this;
    }
}
