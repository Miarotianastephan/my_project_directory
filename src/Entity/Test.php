<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $test_date = null;

    #[ORM\Column]
    private ?float $test_prix = null;

    #[ORM\Column(length: 255)]
    private ?string $test_ref = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTestDate(): ?\DateTimeInterface
    {
        return $this->test_date;
    }

    public function setTestDate(\DateTimeInterface $test_date): static
    {
        $this->test_date = $test_date;

        return $this;
    }

    public function getTestPrix(): ?float
    {
        return $this->test_prix;
    }

    public function setTestPrix(float $test_prix): static
    {
        $this->test_prix = $test_prix;

        return $this;
    }

    public function getTestRef(): ?string
    {
        return $this->test_ref;
    }

    public function setTestRef(string $test_ref): static
    {
        $this->test_ref = $test_ref;

        return $this;
    }
}
