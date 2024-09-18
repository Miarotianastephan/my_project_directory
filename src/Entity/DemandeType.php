<?php

namespace App\Entity;

use App\Repository\DemandeTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: DemandeTypeRepository::class)]
class DemandeType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'demande_type_seq')]
    #[ORM\Column(name: 'dm_type_id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: 'customdate')]
    private ?\DateTimeInterface $dm_date = null;

    #[ORM\Column]
    private ?float $dm_montant = null;

    //na siège na RT
    #[ORM\ManyToOne(targetEntity: PlanCompte::class)]
    #[ORM\JoinColumn(name: "entity_code_id", referencedColumnName: "cpt_id",nullable: false)]
    private ?PlanCompte $entity_code = null; // pour avoir le code de l'entitée 

    #[ORM\Column(length: 255)]
    private ?string $dm_mode_paiement = null;

    #[ORM\Column(length: 255)]
    private ?string $ref_demande = null;

    #[ORM\Column]
    private ?int $dm_etat = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: "utilisateur_id", referencedColumnName: "user_id", nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(targetEntity: PlanCompte::class)]
    #[ORM\JoinColumn(name: "plan_compte_id", referencedColumnName: "cpt_id",nullable: false)]
    private ?PlanCompte $plan_compte = null; // pour avoir le numéro du compte pour l'opération de demande

    /*
        Récupération dynamique lors d'insertion d'un nouveau demande
    */
    #[ORM\ManyToOne(targetEntity: Exercice::class)]
    #[ORM\JoinColumn(name: "exercice_id",referencedColumnName: "exercice_id",nullable: false)]
    private ?Exercice $exercice = null; 

    #[ORM\ManyToOne(targetEntity: Demande::class)]
    #[ORM\JoinColumn(name: "dm_id", referencedColumnName: "dm_id", nullable: false)]
    private ?Demande $demande = null;


    /**
     * @var Collection<int, LogDemandeType>
     */
    #[ORM\OneToMany(targetEntity: LogDemandeType::class, mappedBy: 'demande_type')]
    private Collection $logDemandeTypes;

    /**
     * @var Collection<int, DetailDemandePiece>
     */
    #[ORM\OneToMany(targetEntity: DetailDemandePiece::class, mappedBy: 'demande_type')]
    private Collection $detailDemandePieces;

    #[ORM\Column(nullable: false,options: ["default" => 0])]
    private ?float $montant_reel = 0;

    #[ORM\Column(type: 'customdate')]
    private $dm_date_operation = null;

    // #[ORM\Column(nullable: true)]
    // private ?int $mere_id = null;

    public function __construct()
    {
        $this->logDemandeTypes = new ArrayCollection();
        $this->detailDemandePieces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDmDate(): ?\DateTimeInterface
    {
        return $this->dm_date;
    }

    public function setDmDate(\DateTimeInterface $dm_date): static
    {
        $this->dm_date = $dm_date;

        return $this;
    }

    public function getDmMontant(): ?float
    {
        return $this->dm_montant;
    }

    public function setDmMontant(float $dm_montant): static
    {
        $this->dm_montant = $dm_montant;

        return $this;
    }

    public function getEntityCode(): ?PlanCompte
    {
        return $this->entity_code;
    }

    public function setEntityCode(PlanCompte $entity_code): static
    {
        $this->entity_code = $entity_code;
        return $this;
    }

    public function getDmModePaiement(): ?string
    {
        return $this->dm_mode_paiement;
    }

    public function setDmModePaiement(string $dm_mode_paiement): static
    {
        $this->dm_mode_paiement = $dm_mode_paiement;

        return $this;
    }

    public function getRefDemande(): ?string
    {
        return $this->ref_demande;
    }

    public function setRefDemande(string $ref_demande): static
    {
        $this->ref_demande = $ref_demande;

        return $this;
    }

    public function getDmEtat(): ?int
    {
        return $this->dm_etat;
    }

    public function setDmEtat(int $dm_etat): static
    {
        $this->dm_etat = $dm_etat;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function getPlanCompte(): ?PlanCompte
    {
        return $this->plan_compte;
    }

    public function setPlanCompte(?PlanCompte $plan_compte): static
    {
        $this->plan_compte = $plan_compte;

        return $this;
    }

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): static
    {
        $this->exercice = $exercice;

        return $this;
    }

    public function getDemande(): ?Demande
    {
        return $this->demande;
    }

    public function setDemande(?Demande $demande): static
    {
        $this->demande = $demande;

        return $this;
    }


    /**
     * @return Collection<int, LogDemandeType>
     */
    public function getLogDemandeTypes(): Collection
    {
        return $this->logDemandeTypes;
    }

    public function addLogDemandeType(LogDemandeType $logDemandeType): static
    {
        if (!$this->logDemandeTypes->contains($logDemandeType)) {
            $this->logDemandeTypes->add($logDemandeType);
            $logDemandeType->setDemandeType($this);
        }

        return $this;
    }

    public function removeLogDemandeType(LogDemandeType $logDemandeType): static
    {
        if ($this->logDemandeTypes->removeElement($logDemandeType)) {
            // set the owning side to null (unless already changed)
            if ($logDemandeType->getDemandeType() === $this) {
                $logDemandeType->setDemandeType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DetailDemandePiece>
     */
    public function getDetailDemandePieces(): Collection
    {
        return $this->detailDemandePieces;
    }

    public function addDetailDemandePiece(DetailDemandePiece $detailDemandePiece): static
    {
        if (!$this->detailDemandePieces->contains($detailDemandePiece)) {
            $this->detailDemandePieces->add($detailDemandePiece);
            $detailDemandePiece->setDemandeType($this);
        }

        return $this;
    }

    public function removeDetailDemandePiece(DetailDemandePiece $detailDemandePiece): static
    {
        if ($this->detailDemandePieces->removeElement($detailDemandePiece)) {
            // set the owning side to null (unless already changed)
            if ($detailDemandePiece->getDemandeType() === $this) {
                $detailDemandePiece->setDemandeType(null);
            }
        }

        return $this;
    }

    public function getMontantReel(): ?float
    {
        return $this->montant_reel;
    }

    public function setMontantReel(?float $montant_reel): static
    {
        $this->montant_reel = $montant_reel;

        return $this;
    }

    // public function getMereId(): ?int
    // {
    //     return $this->mere_id;
    // }

    // public function setMereId(?int $mere_id): static
    // {
    //     $this->mere_id = $mere_id;

    //     return $this;
    // }

    public function getDmDateOperation()
    {
        return $this->dm_date_operation;
    }

    public function setDmDateOperation($dm_date_operation): static
    {
        $this->dm_date_operation = $dm_date_operation;

        return $this;
    }
}
