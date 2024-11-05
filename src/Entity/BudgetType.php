<?php

namespace App\Entity;

use App\Repository\BudgetTypeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BudgetTypeRepository::class)]
#[ORM\Table(name: "ce_budget_type")]
class BudgetType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'budget_type_seq')]
    #[ORM\Column(name: 'budget_type_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private ?string $libelle = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }


}
