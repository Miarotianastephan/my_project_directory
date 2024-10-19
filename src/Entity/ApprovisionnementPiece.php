<?php

namespace App\Entity;

use App\Repository\ApprovisionnementPieceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApprovisionnementPieceRepository::class)]
#[ORM\Table(name: "ce_approvisionnement_piece")]
class ApprovisionnementPiece
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'approvisionnement_piece_seq')]
    #[ORM\Column(name: 'approvisionnement_piece_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: 'customdate', nullable:false)]
    private ?\DateTimeInterface $date_ajout = null;

    #[ORM\Column(length: 255)]
    private ?string $ref_approvisionnement = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_fichier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->date_ajout;
    }

    public function setDateAjout(\DateTimeInterface $date_ajout): static
    {
        $this->date_ajout = $date_ajout;

        return $this;
    }

    public function getRefApprovisionnement(): ?string
    {
        return $this->ref_approvisionnement;
    }

    public function setRefApprovisionnement(string $ref_approvisionnement): static
    {
        $this->ref_approvisionnement = $ref_approvisionnement;

        return $this;
    }

    public function getNomFichier(): ?string
    {
        return $this->nom_fichier;
    }

    public function setNomFichier(string $nom_fichier): static
    {
        $this->nom_fichier = $nom_fichier;

        return $this;
    }
}
