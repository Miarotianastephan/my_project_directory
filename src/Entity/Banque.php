<?php

namespace App\Entity;

use App\Repository\BanqueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BanqueRepository::class)]
class Banque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'banque_seq')]
    #[ORM\Column(name: 'banque_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 255,unique: true)]
    private ?string $nom_banque = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomBanque(): ?string
    {
        return $this->nom_banque;
    }

    public function setNomBanque(string $nom_banque): static
    {
        $this->nom_banque = $nom_banque;

        return $this;
    }
}
