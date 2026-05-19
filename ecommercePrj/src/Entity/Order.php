<?php
// src/Entity/Order.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\OrderRepository;
#[ORM\Table(name: "orders")]
#[ORM\Entity(repositoryClass: OrderRepository::class)]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Produit::class)]
    private ?Produit $produit = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $vendeur = null;

    #[ORM\Column(length: 50)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int { return $this->id; }
    public function getProduit(): ?Produit { return $this->produit; }
    public function setProduit(?Produit $produit): self { $this->produit = $produit; return $this; }
    public function getVendeur(): ?User { return $this->vendeur; }
    public function setVendeur(?User $vendeur): self { $this->vendeur = $vendeur; return $this; }
    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }
    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): self { $this->createdAt = $createdAt; return $this; }
}
