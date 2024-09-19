<?php

namespace App\Entity;

use App\Repository\DetailDemandePieceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: DetailDemandePieceRepository::class)]
class DetailDemandePiece
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'detail_dm_type_seq')]
    #[ORM\Column(name: 'detail_dm_type_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $det_dm_piece_url = null;

    #[ORM\Column(length: 255)]
    private ?string $det_dm_type_url = null;

    #[ORM\Column(type: 'customdate', nullable:false)]
    private ?\DateTimeInterface $det_dm_date = null;

    #[ORM\ManyToOne(targetEntity: DemandeType::class)]
    #[ORM\JoinColumn(name: "demande_type_id", referencedColumnName: "dm_type_id",nullable: false)]
    private ?DemandeType $demande_type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDetDmPieceUrl(): ?string
    {
        return $this->det_dm_piece_url;
    }

    public function setDetDmPieceUrl(string $det_dm_piece_url): static
    {
        $this->det_dm_piece_url = $det_dm_piece_url;

        return $this;
    }

    public function getDetDmTypeUrl(): ?string
    {
        return $this->det_dm_type_url;
    }

    public function setDetDmTypeUrl(string $det_dm_type_url): static
    {
        $this->det_dm_type_url = $det_dm_type_url;

        return $this;
    }

    public function getDetDmDate(): ?\DateTimeInterface
    {
        return $this->det_dm_date;
    }

    public function setDetDmDate(\DateTimeInterface $det_dm_date): static
    {
        $this->det_dm_date = $det_dm_date;

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
