<?php

namespace App\Entity;

use App\Repository\LineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LineRepository::class)]
class Line
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Dose", inversedBy: "line")]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    private Dose $dose;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Product")]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "lines")]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private User $user;

    #[ORM\Column(type: "float", scale: 1, nullable: true)]
    private float $qty;

    #[ORM\Column(type: "string", length: 20, nullable: false)]
    private string $day;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDose(): ?Dose
    {
        return $this->dose;
    }

    public function setDose(?Dose $dose): self
    {
        $this->dose = $dose;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function __toString()
    {
        return $this->product->getName() . " " . $this->dose;
    }

    public function getQty(): ?float
    {
        return $this->qty;
    }

    public function setQty(?float $qty): self
    {
        $this->qty = $qty;

        return $this;
    }

    public function getName()
    {
        return $this->getQty() . " " . $this->getProduct()->getName() . " - " . $this->getDose();
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(string $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }


}
