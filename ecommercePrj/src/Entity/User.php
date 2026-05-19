<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 4, max: 50)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
    pattern: "/^(06|07|05)[0-9]{8}$/",
    message: "Numéro de téléphone invalide."
      )]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rib = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $isVerified = false;


    #[ORM\Column(options: ['default' => false])]
    private bool $isApproved = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $token = null;

    /**
     * @var Collection<int, Panier>
     */
    #[ORM\OneToMany(targetEntity: Panier::class, mappedBy: 'User')]
    private Collection $paniers;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'user')]
    private Collection $comments;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'vendor')]
    private Collection $produits;

   
public function __construct()
{
    $this->createdAt = new \DateTimeImmutable();
    $this->roles = ['ROLE_CLIENT'];
    $this->isVerified = false;
    $this->isApproved = false;
    $this->paniers = new ArrayCollection();
    $this->comments = new ArrayCollection();
    $this->produits = new ArrayCollection();
    
}

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
   public function getUserIdentifier(): string
{
    return (string) $this->username ?: $this->email;
}

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
{
    return $this->lastName;
}

public function setLastName(string $lastName): static
{
    $this->lastName = $lastName;

    return $this;
}


    public function getUsername(): ?string
{
    return $this->username;
}

public function setUsername(string $username): static
{
    $this->username = $username;
    return $this;
}

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

   public function getAddress(): ?string
{
    return $this->address;
}

public function setAddress(?string $address): static
{
    $this->address = $address;
    return $this;
}

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getRib(): ?string
    {
        return $this->rib;
    }

    public function setRib(?string $rib): static
    {
        $this->rib = $rib;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(?bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function isApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): static
    {
        $this->isApproved = $isApproved;

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
    public function getToken(): ?string
{
    return $this->token;
}

public function setToken(?string $token): static
{
    $this->token = $token;
    return $this;
}

/**
 * @return Collection<int, Panier>
 */
public function getPaniers(): Collection
{
    return $this->paniers;
}

public function addPanier(Panier $panier): static
{
    if (!$this->paniers->contains($panier)) {
        $this->paniers->add($panier);
        $panier->setUser($this);
    }

    return $this;
}

public function removePanier(Panier $panier): static
{
    if ($this->paniers->removeElement($panier)) {
        // set the owning side to null (unless already changed)
        if ($panier->getUser() === $this) {
            $panier->setUser(null);
        }
    }

    return $this;
}

/**
 * @return Collection<int, Comment>
 */
public function getComments(): Collection
{
    return $this->comments;
}

public function addComment(Comment $comment): static
{
    if (!$this->comments->contains($comment)) {
        $this->comments->add($comment);
        $comment->setUser($this);
    }

    return $this;
}

public function removeComment(Comment $comment): static
{
    if ($this->comments->removeElement($comment)) {
        // set the owning side to null (unless already changed)
        if ($comment->getUser() === $this) {
            $comment->setUser(null);
        }
    }

    return $this;
}

/**
 * @return Collection<int, Produit>
 */
public function getProduits(): Collection
{
    return $this->produits;
}

public function addProduit(Produit $produit): static
{
    if (!$this->produits->contains($produit)) {
        $this->produits->add($produit);
        $produit->setVendor($this);
    }

    return $this;
}

public function removeProduit(Produit $produit): static
{
    if ($this->produits->removeElement($produit)) {
        // set the owning side to null (unless already changed)
        if ($produit->getVendor() === $this) {
            $produit->setVendor(null);
        }
    }

    return $this;
}


}
