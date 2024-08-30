<?php

namespace App\Entity;

use App\Repository\LogDemandeTypeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogDemandeTypeRepository::class)]
class LogDemandeType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'log_etat_demande_seq')]
    #[ORM\Column(name: 'log_dm_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $log_dm_date = null;

    #[ORM\Column]
    private ?int $dm_etat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $log_dm_observation = null;

    #[ORM\Column(length: 255)]
    private ?string $user_matricule = null;

    #[ORM\ManyToOne(targetEntity: DemandeType::class)]
    #[ORM\JoinColumn(name: "demande_type_id", referencedColumnName: "dm_type_id",nullable: false)]
    private ?DemandeType $demande_type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogDmDate(): ?\DateTimeInterface
    {
        return $this->log_dm_date;
    }

    public function setLogDmDate(\DateTimeInterface $log_dm_date): static
    {
        $this->log_dm_date = $log_dm_date;

        return $this;
    }

    public function getDmEtat(): ?int
    {
        return $this->dm_etat;
    }

    public function setDmEtat(int $dm_etat): static
    {
        $this->dm_etat = $dm_etat;

        return $this;
    }

    public function getLogDmObservation(): ?string
    {
        return $this->log_dm_observation;
    }

    public function setLogDmObservation(?string $log_dm_observation): static
    {
        $this->log_dm_observation = $log_dm_observation;

        return $this;
    }

    public function getUserMatricule(): ?string
    {
        return $this->user_matricule;
    }

    public function setUserMatricule(string $user_matricule): static
    {
        $this->user_matricule = $user_matricule;

        return $this;
    }

    public function getDemandeType(): ?DemandeType
    {
        return $this->demande_type;
    }

    public function setDemandeType(?DemandeType $demande_type): static
    {
        $this->demande_type = $demande_type;
        return $this;
    }
}
