<?php

namespace App\Entity;

use App\Repository\TransactionTypeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionTypeRepository::class)]
class TransactionType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'trs_seq')]
    #[ORM\Column(name: 'trs_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $trs_code = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $trs_definition = null;

    #[ORM\Column(length: 255)]
    private ?string $trs_libelle = null;

    public function setId(int $id): void{
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrsCode(): ?string
    {
        return $this->trs_code;
    }

    public function setTrsCode(string $trs_code): static
    {
        $this->trs_code = $trs_code;

        return $this;
    }

    public function getTrsDefinition(): ?string
    {
        return $this->trs_definition;
    }

    public function setTrsDefinition(?string $trs_definition): static
    {
        $this->trs_definition = $trs_definition;

        return $this;
    }

    public function getTrsLibelle(): ?string
    {
        return $this->trs_libelle;
    }

    public function setTrsLibelle(string $trs_libelle): static
    {
        $this->trs_libelle = $trs_libelle;

        return $this;
    }

}
