<?php

namespace App\Entity;

use App\Repository\DetailTransactionCompteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetailTransactionCompteRepository::class)]
class DetailTransactionCompte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\SequenceGenerator(sequenceName: 'detail_trs_cpt_seq')]
    #[ORM\Column(name: 'det_trs_cpt_id', type: Types::INTEGER)]
    private ?int $id = null;

    /**
     * @var Collection<int, TransactionType>
     */
    #[ORM\OneToMany(targetEntity: TransactionType::class, mappedBy: 'detailTransactionCompte')]
    private Collection $transaction_type;

    /**
     * @var Collection<int, PlanCompte>
     */
    #[ORM\OneToMany(targetEntity: PlanCompte::class, mappedBy: 'detailTransactionCompte')]
    private Collection $plan_compte;


    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, TransactionType>
     */
    public function getTransactionType(): Collection
    {
        return $this->transaction_type;
    }

    /**
     * @param Collection $transaction_type
     */
    public function setTransactionType(Collection $transaction_type): void
    {
        $this->transaction_type = $transaction_type;
    }

    /**
     * @return Collection
     */
    public function getPlanCompte(): Collection
    {
        return $this->plan_compte;
    }
    /**
     * @param Collection $plan_compte
     */
    public function setPlanCompte(Collection $plan_compte): void
    {
        $this->plan_compte = $plan_compte;
    }
    public function __toString(): string
    {
        $transactionTypes = implode(', ', $this->transaction_type->map(fn($t) => (string)$t)->toArray());
        $planComptes = implode(', ', $this->plan_compte->map(fn($p) => (string)$p)->toArray());

        return sprintf(
            "DetailTransactionCompte(ID: %d, Transaction Types: [%s], Plan Comptes: [%s])",
            $this->id ?? 0,
            $transactionTypes,
            $planComptes
        );
    }

}
