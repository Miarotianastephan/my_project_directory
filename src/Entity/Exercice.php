<?php

namespace App\Entity;

use App\Repository\ExerciceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExerciceRepository::class)]
#[ORM\Table(name: "ce_exercice")]
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

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private ?bool $is_valid = false; // TRUE : 1 , FALSE : 0

    public function __toString(): string
    {
        $debut = $this->exercice_date_debut ? $this->exercice_date_debut->format('d-M-Y') : 'N/A';
        $fin = $this->exercice_date_fin ? $this->exercice_date_fin->format('d-m-Y') : 'N/A';
        return sprintf('Exercice du %s au %s', $debut, $fin);
    }

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

    public function isValid(): ?bool
    {
        return $this->is_valid;
    }

    public function setValid(bool $is_valid): static
    {
        $this->is_valid = $is_valid;

        return $this;
    }
}
