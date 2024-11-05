<?php

namespace App\Entity;

use App\Repository\ObservationDemandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObservationDemandeRepository::class)]
#[ORM\Table(name: 'ce_observation_demande')]
class ObservationDemande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $matricule_observateur = null;

    #[ORM\Column(length: 255)]
    private ?string $observation = null;

    #[ORM\Column(length: 255)]
    private ?string $ref_demande = null;

    #[ORM\Column(type: 'customdate', nullable: true)]
    private ?\DateTimeInterface $date_observation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatriculeObservateur(): ?string
    {
        return $this->matricule_observateur;
    }

    public function setMatriculeObservateur(string $matricule_observateur): static
    {
        $this->matricule_observateur = $matricule_observateur;

        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(string $observation): static
    {
        $this->observation = $observation;

        return $this;
    }

    public function getRefDemande(): ?string
    {
        return $this->ref_demande;
    }

    public function setRefDemande(string $ref_demande): static
    {
        $this->ref_demande = $ref_demande;

        return $this;
    }

    public function getDateObservation(): ?\DateTimeInterface
    {
        return $this->date_observation;
    }

    public function setDateObservation(?\DateTimeInterface $date_observation): static
    {
        $this->date_observation = $date_observation;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            ' [ RefDemande: %s, Matricule: %s, Observation: %s, Date: %s]',
            $this->ref_demande,
            $this->matricule_observateur,
            $this->observation,
            $this->date_observation ? $this->date_observation->format('Y-m-d H:i:s') : 'N/A'
        );
    }

}
