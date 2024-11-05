<?php

namespace App\Entity;

use App\Repository\GroupeUtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: GroupeUtilisateurRepository::class)]
#[ORM\Table(name: 'ce_groupe_utilisateur')]
class GroupeUtilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"SEQUENCE")]
    #[ORM\SequenceGenerator(sequenceName: 'grp_seq', allocationSize: 1, initialValue: 1)]
    #[ORM\Column(name: 'grp_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $grp_libelle;

    #[ORM\Column]
    private ?int $grp_niveau;

    /**
     * @var Collection<int, Utilisateur>
     */
    // #[MaxDepth(1)]
    #[Ignore] // Annotation pour éviter les boucles infinis
    #[ORM\OneToMany(targetEntity: Utilisateur::class, mappedBy: 'group_utilisateur')]
    private Collection $utilisateurs;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGrpLibelle(): ?string
    {
        return $this->grp_libelle;
    }

    public function setGrpLibelle(string $grp_libelle): static
    {
        $this->grp_libelle = $grp_libelle;

        return $this;
    }

    public function getGrpNiveau(): ?int
    {
        return $this->grp_niveau;
    }

    public function setGrpNiveau(int $grp_niveau): static
    {
        $this->grp_niveau = $grp_niveau;

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): static
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->add($utilisateur);
            $utilisateur->setGroupUtilisateur($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getGroupUtilisateur() === $this) {
                $utilisateur->setGroupUtilisateur(null);
            }
        }

        return $this;
    }
}
