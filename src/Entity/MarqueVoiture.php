<?php

namespace App\Entity;

use App\Repository\MarqueVoitureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MarqueVoitureRepository::class)]
#[ORM\Table(name: "marque_voiture")]
class MarqueVoiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"SEQUENCE")]
    #[ORM\SequenceGenerator(sequenceName: "mrq_seq", allocationSize: 1, initialValue: 1)]
    #[ORM\Column(name:"mrq_id")]
    private ?int $id = null;

    #[ORM\Column(length: 1000 , name:"mrq_libelle", nullable:false)]
    private ?string $marque_libelle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarqueLibelle(): ?string
    {
        return $this->marque_libelle;
    }

    public function setMarqueLibelle(string $marque_libelle): static
    {
        $this->marque_libelle = $marque_libelle;

        return $this;
    }
}
