<?php

namespace App\Entity;

use App\Repository\DetailBudgetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailBudgetRepository::class)]
class DetailBudget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'detail_budget_seq')]
    #[ORM\Column(name: 'detail_budget_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $budget_montant = null;

    #[ORM\Column(type: 'customdate')]
    private ?\DateTimeInterface $budget_date = null;

    #[ORM\ManyToOne(targetEntity: BudgetType::class)]
    #[ORM\JoinColumn(name: "budget_type_id", referencedColumnName: "budget_type_id",nullable: false)]
    private ?BudgetType $budget_type = null;

    #[ORM\ManyToOne(targetEntity: Exercice::class)]
    #[ORM\JoinColumn(name: "exercice_id", referencedColumnName: "exercice_id",nullable: false)]
    private ?Exercice $exercice = null;

    #[ORM\ManyToOne(targetEntity: CompteMere::class)]
    #[ORM\JoinColumn(name: "compte_mere_id", referencedColumnName: "id",nullable: false)]
    private ?CompteMere $compte_mere = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBudgetMontant(): ?float
    {
        return $this->budget_montant;
    }

    public function setBudgetMontant(?float $budget_montant): static
    {
        $this->budget_montant = $budget_montant;

        return $this;
    }

    public function getBudgetDate(): ?\DateTimeInterface
    {
        return $this->budget_date;
    }

    public function setBudgetDate(\DateTimeInterface $budget_date): static
    {
        $this->budget_date = $budget_date;

        return $this;
    }

    public function getBudgetType(): ?BudgetType
    {
        return $this->budget_type;
    }

    public function setBudgetType(?BudgetType $budget_type): static
    {
        $this->budget_type = $budget_type;

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
    public function getCompteMere(): ?CompteMere
    {
        return $this->compte_mere;
    }

    public function setCompteMere(?CompteMere $compte_mere): static
    {
        $this->compte_mere = $compte_mere;

        return $this;
    }
}
