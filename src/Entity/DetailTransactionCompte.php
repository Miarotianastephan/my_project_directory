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

    #[ORM\ManyToOne(targetEntity: TransactionType::class)]
    #[ORM\JoinColumn(name: "transaction_type_id", referencedColumnName: "trs_id", nullable: false)]
    private ?TransactionType $transaction_type = null;

    #[ORM\ManyToOne(targetEntity: PlanCompte::class)]
    #[ORM\JoinColumn(name: "plan_compte_id", referencedColumnName: "cpt_id", nullable: false)]
    private ?PlanCompte $plan_compte = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private ?bool $isTrsDebit = null; // TRUE : 1 , FALSE : 0


    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return TransactionType|null
     */
    public function getTransactionType(): ?TransactionType
    {
        return $this->transaction_type;
    }

    /**
     * @param TransactionType|null $transaction_type
     */
    public function setTransactionType(?TransactionType $transaction_type): void
    {
        $this->transaction_type = $transaction_type;
    }

    /**
     * @return PlanCompte|null
     */
    public function getPlanCompte(): ?PlanCompte
    {
        return $this->plan_compte;
    }

    /**
     * @param PlanCompte|null $plan_compte
     */
    public function setPlanCompte(?PlanCompte $plan_compte): void
    {
        $this->plan_compte = $plan_compte;
    }

    public function isTrsDebit(): ?bool
    {
        return $this->isTrsDebit;
    }

    public function setTrsDebit(bool $isTrsDebit): static
    {
        $this->isTrsDebit = $isTrsDebit;

        return $this;
    }

}
