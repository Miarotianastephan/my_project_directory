<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'demande_seq')]
    #[ORM\Column(name: 'dm_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column]
    private ?int $dm_code = null;

    public function __construct(){ }

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

    public function getDmCode(): ?int
    {
        return $this->dm_code;
    }

    public function setDmCode(int $dm_code): static
    {
        $this->dm_code = $dm_code;

        return $this;
    }


}
