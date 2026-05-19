<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?Produit $produit = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?User $user = null;

     #[ORM\Column(length: 20, nullable: true)]
      private ?string $sentiment = null;


    #[ORM\Column(type: Types::FLOAT, nullable: true)]
     private ?float $score = null;


     #[ORM\Column(type: 'boolean')]
     private bool $isOwner = false;




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;

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

public function getSentiment(): ?string
{
    return $this->sentiment;
}

public function setSentiment(?string $sentiment): self
{
    $this->sentiment = $sentiment;
    return $this;
}
public function getScore(): ?float
{
    return $this->score;
}

public function setScore(?float $score): self
{
    $this->score = $score;
    return $this;
}
public function getIsOwner(): bool
{
    return $this->isOwner;
}

public function setIsOwner(bool $isOwner): self
{
    $this->isOwner = $isOwner;
    return $this;
}

}
