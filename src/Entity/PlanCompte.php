<?php

namespace App\Entity;

use App\Repository\PlanCompteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: PlanCompteRepository::class)]
#[ORM\Table(name: 'plan_compte')]
class PlanCompte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'cpt_seq')]
    #[ORM\Column(name: 'cpt_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique:true)]
    private ?string $cpt_numero = null;

    #[ORM\Column(length: 255, unique:true)]
    private ?string $cpt_libelle = null;

    #[ORM\ManyToOne(inversedBy: 'planComptes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CompteMere $compte_mere = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCptNumero(): ?string
    {
        return $this->cpt_numero;
    }

    public function setCptNumero(string $cpt_numero)
    {
        $cpt_numero = trim($cpt_numero);
        if(($cpt_numero == null) || (strlen($cpt_numero) == 0)){
            throw new InvalidArgumentException("Le numéro doit contenir au moins 6 caractères.");
        }
        if($this->cpt_numero != $cpt_numero){
            $this->cpt_numero = $cpt_numero;
            return $this;
        }
        return false;
    }

    public function getCptLibelle(): ?string
    {
        return $this->cpt_libelle;
    }

    public function setCptLibelle(string $cpt_libelle)
    {
        $cpt_libelle = trim($cpt_libelle);
        if(($cpt_libelle == null) || (strlen($cpt_libelle) == 0)){
            throw new InvalidArgumentException("Le libelle doit contenir au moins 3 caractères.");
        }
        if($this->cpt_libelle != $cpt_libelle){
            $this->cpt_libelle = $cpt_libelle;
            return $this;
        }
        return false;
    }

    public function getCompteMere(): ?CompteMere
    {
        return $this->compte_mere;
    }

    public function setCompteMere(?CompteMere $compte_mere): static
    {
        $this->compte_mere = $compte_mere;

        return $this;
    }
}
