<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'evn_seq')]
    #[ORM\Column(name: 'evn_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "evn_trs_id", referencedColumnName: "trs_id",nullable: false)]
    private ?TransactionType $evn_trs_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "evn_responsable_id", referencedColumnName: "user_id",nullable: false)]
    private ?Utilisateur $evn_responsable = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "evn_exercice_id", referencedColumnName: "exercice_id",nullable: false)]
    private ?Exercice $evn_exercice = null;

    //libelle entité
    #[ORM\Column(length: 255)]
    private ?string $evn_code_entity = null;

    #[ORM\Column]
    private ?float $evn_montant = null;

    #[ORM\Column(length: 255)]
    private ?string $evn_reference = null;

    #[ORM\Column(type: 'customdate')]
    private $evn_date_operation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvnTrsId(): ?TransactionType
    {
        return $this->evn_trs_id;
    }

    public function setEvnTrsId(?TransactionType $evn_trs_id): static
    {
        $this->evn_trs_id = $evn_trs_id;

        return $this;
    }

    public function getEvnResponsable(): ?Utilisateur
    {
        return $this->evn_responsable;
    }

    public function setEvnResponsable(?Utilisateur $evn_responsable): static
    {
        $this->evn_responsable = $evn_responsable;

        return $this;
    }

    public function getEvnExercice(): ?Exercice
    {
        return $this->evn_exercice;
    }

    public function setEvnExercice(?Exercice $evn_exercice): static
    {
        $this->evn_exercice = $evn_exercice;

        return $this;
    }

    public function getEvnCodeEntity(): ?string
    {
        return $this->evn_code_entity;
    }

    public function setEvnCodeEntity(string $evn_code_entity): static
    {
        $this->evn_code_entity = $evn_code_entity;

        return $this;
    }

    public function getEvnMontant(): ?float
    {
        return $this->evn_montant;
    }

    public function setEvnMontant(float $evn_montant): static
    {
        $this->evn_montant = $evn_montant;

        return $this;
    }

    public function getEvnReference(): ?string
    {
        return $this->evn_reference;
    }

    public function setEvnReference(string $evn_reference): static
    {
        $this->evn_reference = $evn_reference;

        return $this;
    }

    public function getEvnDateOperation()
    {
        return $this->evn_date_operation;
    }

    public function setEvnDateOperation($evn_date_operation): static
    {
        $this->evn_date_operation = $evn_date_operation;

        return $this;
    }
}
