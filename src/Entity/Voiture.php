<?php

namespace App\Entity;

use App\Repository\VoitureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoitureRepository::class)]
#[ORM\Table(name: 'voiture')]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"SEQUENCE")]
    #[ORM\SequenceGenerator(sequenceName: 'voiture_seq', allocationSize: 1, initialValue: 1)]
    #[ORM\Column(name:"voiture_id")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $voiture_libelle = null;

    #[ORM\Column(name:"nb_place")]
    private ?int $place = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVoitureLibelle(): ?string
    {
        return $this->voiture_libelle;
    }

    public function setVoitureLibelle(string $voiture_libelle): static
    {
        $this->voiture_libelle = $voiture_libelle;

        return $this;
    }

    public function getPlace(): ?int
    {
        return $this->place;
    }

    public function setPlace(int $place): static
    {
        $this->place = $place;

        return $this;
    }
}
