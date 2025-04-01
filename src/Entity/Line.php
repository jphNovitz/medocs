<?php

namespace App\Entity;

use App\Repository\LineRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LineRepository::class)]
class Line
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Dose", inversedBy: "line")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Veuillez sélectionner une Posologie.")]
    private Dose $dose;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Product")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "Veuillez sélectionner un produit.")]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "lines")]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private User $user;

    #[ORM\Column(type: "float", scale: 1, nullable: false)]
    private float $qty = 1;

    #[ORM\Column(type: "json", nullable: false)]
    private array $days = [0];


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


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDays(): array
    {
        return $this->days;
    }

    public function setDays(array $days): static
    {
        $this->days = $days;

        return $this;
    }
}
