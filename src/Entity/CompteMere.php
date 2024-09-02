<?php

namespace App\Entity;

use App\Repository\CompteMereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompteMereRepository::class)]
class CompteMere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $cpt_numero = null;

    #[ORM\Column(length: 255)]
    private ?string $cpt_libelle = null;

    /**
     * @var Collection<int, PlanCompte>
     */
    #[ORM\OneToMany(targetEntity: PlanCompte::class, mappedBy: 'compte_mere')]
    private Collection $planComptes;

    public function __construct()
    {
        $this->planComptes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCptNumero(): ?string
    {
        return $this->cpt_numero;
    }

    public function setCptNumero(string $cpt_numero): static
    {
        $this->cpt_numero = $cpt_numero;

        return $this;
    }

    public function getCptLibelle(): ?string
    {
        return $this->cpt_libelle;
    }

    public function setCptLibelle(string $cpt_libelle): static
    {
        $this->cpt_libelle = $cpt_libelle;

        return $this;
    }

    /**
     * @return Collection<int, PlanCompte>
     */
    public function getPlanComptes(): Collection
    {
        return $this->planComptes;
    }

    public function addPlanCompte(PlanCompte $planCompte): static
    {
        if (!$this->planComptes->contains($planCompte)) {
            $this->planComptes->add($planCompte);
            $planCompte->setCompteMere($this);
        }

        return $this;
    }

    public function removePlanCompte(PlanCompte $planCompte): static
    {
        if ($this->planComptes->removeElement($planCompte)) {
            // set the owning side to null (unless already changed)
            if ($planCompte->getCompteMere() === $this) {
                $planCompte->setCompteMere(null);
            }
        }

        return $this;
    }
}
