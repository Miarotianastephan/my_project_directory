<?php

namespace App\Entity;

use App\Repository\UsageChequeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsageChequeRepository::class)]
class UsageCheque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'usage_chq_seq')]
    #[ORM\Column(name: 'usage_chq_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $chq_montant = null;

    //etat = 1 = true ->valide ; etat = 1 = false ->
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private ?bool $is_valid = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $chq_beneficiaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $chq_remettant = null;

    #[ORM\Column(length: 255)]
    private ?string $chq_numero = null;

    #[ORM\Column(type: 'customdate')]
    private ?\DateTimeInterface $date_usage = null;

    #[ORM\ManyToOne(targetEntity: Banque::class)]
    #[ORM\JoinColumn(name: "banque_id", referencedColumnName: "banque_id", nullable: true)]
    private ?Banque $banque = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChqMontant(): ?float
    {
        return $this->chq_montant;
    }

    public function setChqMontant(float $chq_montant): static
    {
        $this->chq_montant = $chq_montant;

        return $this;
    }

    public function getIsValid(): ?bool
    {
        return $this->is_valid;
    }
    public function setIsValid(?bool $is_valid): void
    {
        $this->is_valid = $is_valid;
    }

    public function getChqBeneficiaire(): ?string
    {
        return $this->chq_beneficiaire;
    }

    public function setChqBeneficiaire(?string $chq_beneficiaire): static
    {
        $this->chq_beneficiaire = $chq_beneficiaire;

        return $this;
    }

    public function getChqRemettant(): ?string
    {
        return $this->chq_remettant;
    }

    public function setChqRemettant(?string $chq_remettant): static
    {
        $this->chq_remettant = $chq_remettant;

        return $this;
    }

    public function getChqNumero(): ?string
    {
        return $this->chq_numero;
    }

    public function setChqNumero(string $chq_numero): static
    {
        $this->chq_numero = $chq_numero;

        return $this;
    }

    public function getDateUsage(): ?\DateTimeInterface
    {
        return $this->date_usage;
    }

    public function setDateUsage(\DateTimeInterface $date_usage): static
    {
        $this->date_usage = $date_usage;

        return $this;
    }

    public function getBanque(): ?Banque
    {
        return $this->banque;
    }

    public function setBanque(?Banque $banque): static
    {
        $this->banque = $banque;

        return $this;
    }
}
