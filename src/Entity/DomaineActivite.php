<?php

namespace App\Entity;

use App\Repository\DomaineActiviteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DomaineActiviteRepository::class)]
class DomaineActivite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_activite = null;

    #[ORM\Column(nullable: true)]
    private ?int $membre_max = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomActivite(): ?string
    {
        return $this->nom_activite;
    }

    public function setNomActivite(string $nom_activite): static
    {
        $this->nom_activite = $nom_activite;

        return $this;
    }

    public function getMembreMax(): ?int
    {
        return $this->membre_max;
    }

    public function setMembreMax(?int $membre_max): static
    {
        $this->membre_max = $membre_max;

        return $this;
    }
}
