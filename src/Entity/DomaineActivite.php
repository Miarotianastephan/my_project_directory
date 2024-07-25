<?php

namespace App\Entity;

use App\Repository\DomaineActiviteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DomaineActiviteRepository::class)]
#[ORM\Table(name: 'domaine_activite')]
class DomaineActivite
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"SEQUENCE")]
    #[ORM\SequenceGenerator(sequenceName: 'dom_seq', allocationSize: 1, initialValue: 1)]
    #[ORM\Column(name:"dom_id")]
    private ?int $id = null;

    #[ORM\Column(length: 255, name:"dom_nom")]
    private ?string $nom_domaine;

    #[ORM\Column(nullable: true, name:"mbr_max")]
    private ?int $membre_max = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNomDomaine(): ?string
    {
        return $this->nom_domaine;
    }

    public function setNomDomaine(string $nom_domaine): static
    {
        $this->nom_domaine = $nom_domaine;

        return $this;
    }
}
