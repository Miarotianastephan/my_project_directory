<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use PhpParser\Node\Name;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateur')]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"SEQUENCE")]
    #[ORM\SequenceGenerator(sequenceName: 'user_seq', allocationSize: 1, initialValue: 1)]
    #[ORM\Column(name:"user_id")]
    private ?int $id = null;

    #[ORM\Column(name:"user_matricule" ,length: 50)]
    private ?string $user_matricule = null;

    #[ORM\Column(name:"dt_ajout" ,type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_ajout = null;

    #[ORM\ManyToOne(targetEntity:GroupeUtilisateur::class ,inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(name:"grp_id",referencedColumnName:"grp_id",nullable: false)]
    private ?GroupeUtilisateur $group_utilisateur = null;

    #[ORM\ManyToOne(targetEntity: DomaineActivite::class)]
    #[ORM\JoinColumn(name:"dom_id" ,referencedColumnName:"dom_id" ,nullable: false)]
    private ?DomaineActivite $domaine_activite = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->date_ajout;
    }

    public function setDateAjout(\DateTimeInterface $date_ajout): static
    {
        $this->date_ajout = $date_ajout;

        return $this;
    }

    public function getGroupUtilisateur(): ?GroupeUtilisateur
    {
        return $this->group_utilisateur;
    }

    public function setGroupUtilisateur(?GroupeUtilisateur $group_utilisateur): static
    {
        $this->group_utilisateur = $group_utilisateur;

        return $this;
    }

    public function getDomaineActivite(): ?DomaineActivite
    {
        return $this->domaine_activite;
    }

    public function setDomaineActivite(?DomaineActivite $domaine_activite): static
    {
        $this->domaine_activite = $domaine_activite;

        return $this;
    }
}
