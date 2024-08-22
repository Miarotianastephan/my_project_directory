<?php

namespace App\Entity;

use App\Repository\Test2Repository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Test2Repository::class)]
class Test2
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $labelle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabelle(): ?string
    {
        return $this->labelle;
    }

    public function setLabelle(string $labelle): static
    {
        $this->labelle = $labelle;

        return $this;
    }
}
