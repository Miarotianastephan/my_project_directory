<?php

namespace App\Entity;

use App\Repository\VersementsRepository;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: VersementsRepository::class)]
class Versements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_remettant = null;

    #[ORM\Column(type: 'customdate')]
    private $vrsm_date;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column]
    private ?float $vrsm_montant = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "demande_id", referencedColumnName: "dm_type_id",nullable: false)]
    private ?DemandeType $demande_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "utilisateur_id", referencedColumnName: "user_id",nullable: false)]
    private ?Utilisateur $utilisateur_id = null;

    #[ORM\Column(length: 255)]
    private ?string $vrsm_motif = null;

    #[ORM\Column(length: 255)]
    private ?string $vrsm_reference = 'ref_default';

    // Setters
    public function setNomRemettant(string $nom_remettant): static
    {
        if (empty($nom_remettant)) {
            throw new InvalidArgumentException("Le nom du remettant ne peut pas être vide.");
        }
        $this->nom_remettant = $nom_remettant;

        return $this;
    }

    public function setVrsmDate($vrsm_date): static
    {
        $currentDate = new \DateTime();
        if ($vrsm_date > $currentDate) {
            throw new InvalidArgumentException("La date du versement ne peut pas être supérieure à la date actuelle.");
        }
        $this->vrsm_date = $vrsm_date;

        return $this;
    }

    public function setAdresse(string $adresse): static
    {
        if (empty($adresse)) {
            throw new InvalidArgumentException("L'adresse ne peut pas être vide.");
        }
        $this->adresse = $adresse;

        return $this;
    }

    public function setVrsmMontant(float $vrsm_montant): static
    {
        if ($vrsm_montant <= 0) {
            throw new InvalidArgumentException("Le montant du versement doit être supérieur à zéro.");
        }
        $this->vrsm_montant = $vrsm_montant;

        return $this;
    }

    public function setDemandeId(DemandeType $demande_id): static
    {
        if ($demande_id === null) {
            throw new InvalidArgumentException("Le champ demande_id ne peut pas être null.");
        }
        $this->demande_id = $demande_id;

        return $this;
    }

    public function setUtilisateurId(?Utilisateur $utilisateur_id): static
    {
        if ($utilisateur_id === null) {
            throw new InvalidArgumentException("L'utilisateur ne peut pas être null.");
        }
        $this->utilisateur_id = $utilisateur_id;

        return $this;
    }

    public function setVrsmMotif(string $vrsm_motif): static
    {
        if (empty($vrsm_motif)) {
            throw new InvalidArgumentException("Le motif du versement ne peut pas être vide.");
        }
        $this->vrsm_motif = $vrsm_motif;

        return $this;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomRemettant(): ?string
    {
        return $this->nom_remettant;
    }

    public function getVrsmDate()
    {
        return $this->vrsm_date;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function getVrsmMontant(): ?float
    {
        return $this->vrsm_montant;
    }

    public function getDemandeId(): ?DemandeType
    {
        return $this->demande_id;
    }

    public function getUtilisateurId(): ?Utilisateur
    {
        return $this->utilisateur_id;
    }

    public function getVrsmMotif(): ?string
    {
        return $this->vrsm_motif;
    }

    // constructeur
    public function __construct(
        string $nom_remettant,
        $vrsm_date,
        string $adresse,
        float $vrsm_montant,
        DemandeType $demande_id,
        Utilisateur $utilisateur_id,
        string $vrsm_motif
    ) {
        $this->setNomRemettant($nom_remettant);
        $this->setVrsmDate($vrsm_date);
        $this->setAdresse($adresse);
        $this->setVrsmMontant($vrsm_montant);
        $this->setDemandeId($demande_id);
        $this->setUtilisateurId($utilisateur_id);
        $this->setVrsmMotif($vrsm_motif);
    }

    public function getVrsmReference(): ?string
    {
        return $this->vrsm_reference;
    }

    public function setVrsmReference(string $vrsm_reference): static
    {
        $this->vrsm_reference = $vrsm_reference;

        return $this;
    }
}
