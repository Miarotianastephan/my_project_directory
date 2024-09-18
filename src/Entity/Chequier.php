<?php

namespace App\Entity;

use App\Repository\ChequierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChequierRepository::class)]
class Chequier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'chequier_seq')]
    #[ORM\Column(name: 'chequier_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $chequier_numero_debut = null;

    #[ORM\Column(length: 255)]
    private ?string $chequier_numero_fin = null;

    #[ORM\Column(type: 'customdate')]
    private ?\DateTimeInterface $chequier_date_arrivee = null;

    #[ORM\ManyToOne(targetEntity: Banque::class)]
    #[ORM\JoinColumn(name: "banque_id", referencedColumnName: "banque_id", nullable: false)]
    private ?Banque $banque = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChequierNumeroDebut(): ?string
    {
        return $this->chequier_numero_debut;
    }

    public function setChequierNumeroDebut(string $chequier_numero_debut): static
    {
        $this->chequier_numero_debut = $chequier_numero_debut;

        return $this;
    }

    public function getChequierNumeroFin(): ?string
    {
        return $this->chequier_numero_fin;
    }

    public function setChequierNumeroFin(string $chequier_numero_fin): static
    {
        $this->chequier_numero_fin = $chequier_numero_fin;

        return $this;
    }

    public function getChequierDateArrivee(): ?\DateTimeInterface
    {
        return $this->chequier_date_arrivee;
    }

    public function setChequierDateArrivee(\DateTimeInterface $chequier_date_arrivee): static
    {
        $this->chequier_date_arrivee = $chequier_date_arrivee;

        return $this;
    }

    public function getBanque(): ?Banque
    {
        return $this->banque;
    }

    public function setBanque(?Banque $banque): static
    {
        $this->banque = $banque;

        return $this;
    }
}
