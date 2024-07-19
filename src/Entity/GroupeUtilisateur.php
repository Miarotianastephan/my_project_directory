<?php

namespace App\Entity;

use App\Repository\GroupeUtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupeUtilisateurRepository::class)]
#[ORM\Table(name: 'groupe_utilisateur')]
class GroupeUtilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:'SEQUENCE')]
    #[ORM\SequenceGenerator(sequenceName: 'grp_seq', allocationSize: 1, initialValue: 1)]
    #[ORM\Column(name:'grp_id', type:'integer')]
    private ?int $groupeId;

    public function getId(): ?int
    {
        return $this->groupeId;
    }

    #[ORM\Column(name:'grp_libelle' ,type: 'string', nullable: false, unique: true)]
    private $libelle;

    #[ORM\Column(name:'niveau' ,type: 'integer', nullable: false)]
    private $niveau;

    public function getGroupeId(): ?int
    {
        return $this->groupeId;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getNiveau(): ?int
    {
        return $this->niveau;
    }

    public function setNiveau(int $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }
}
