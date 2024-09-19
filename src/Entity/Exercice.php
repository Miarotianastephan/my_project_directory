<?php

namespace App\Entity;

use App\Repository\ExerciceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExerciceRepository::class)]
class Exercice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'exercice_seq')]
    #[ORM\Column(name: 'exercice_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: 'customdate', nullable:false)]
    private ?\DateTimeInterface $exercice_date_debut = null;

    #[ORM\Column(type: 'customdate', nullable: true)]
    private ?\DateTimeInterface $exercice_date_fin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExerciceDateDebut(): ?\DateTimeInterface
    {
        return $this->exercice_date_debut;
    }

    public function setExerciceDateDebut(\DateTimeInterface $exercice_date_debut): static
    {
        $this->exercice_date_debut = $exercice_date_debut;

        return $this;
    }

    public function getExerciceDateFin(): ?\DateTimeInterface
    {
        return $this->exercice_date_fin;
    }

    public function setExerciceDateFin(?\DateTimeInterface $exercice_date_fin): static
    {
        $this->exercice_date_fin = $exercice_date_fin;

        return $this;
    }
}
