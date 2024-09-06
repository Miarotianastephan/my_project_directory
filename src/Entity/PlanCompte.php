<?php

namespace App\Entity;

use App\Repository\PlanCompteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanCompteRepository::class)]
#[ORM\Table(name: 'plan_compte')]
class PlanCompte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'cpt_seq')]
    #[ORM\Column(name: 'cpt_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $cpt_numero = null;

    #[ORM\Column(length: 255)]
    private ?string $cpt_libelle = null;

    public function __construct(int $id,string $cpt_numero, string $cpt_libelle)
    {
        $this->setCptLibelle($cpt_libelle);
        $this->setCptNumero($cpt_numero);
        $this->id = $id;
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


}
