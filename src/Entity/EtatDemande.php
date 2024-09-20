<?php

namespace App\Entity;

use App\Repository\EtatDemandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatDemandeRepository::class)]
class EtatDemande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'etat_dm_seq')]
    #[ORM\Column(name: 'etat_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(unique:true, nullable:false)]
    private ?int $etat_code = null;

    #[ORM\Column(length: 255,unique:true, nullable:false)]
    private ?string $etat_libelle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtatCode(): ?int
    {
        return $this->etat_code;
    }

    public function setEtatCode(int $etat_code): static
    {
        $this->etat_code = $etat_code;

        return $this;
    }

    public function getEtatLibelle(): ?string
    {
        return $this->etat_libelle;
    }

    public function setEtatLibelle(string $etat_libelle): static
    {
        $this->etat_libelle = $etat_libelle;

        return $this;
    }
}
