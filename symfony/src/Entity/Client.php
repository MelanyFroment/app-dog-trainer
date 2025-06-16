<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\Dog;


#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['client:read', 'dog:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['client:read', 'client:write', 'dog:read'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 100)]
    #[Groups(['client:read', 'client:write', 'dog:read'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Groups(['client:read', 'client:write', 'dog:read'])]
    private ?string $address = null;

    #[ORM\Column(length: 20)]
    #[Groups(['client:read', 'client:write', 'dog:read'])]
    private ?string $postalCode = null;

    #[ORM\Column(length: 100)]
    #[Groups(['client:read', 'client:write', 'dog:read'])]
    private ?string $city = null;

    #[ORM\Column(length: 20)]
    #[Groups(['client:read', 'client:write', 'dog:read'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['client:read', 'client:write', 'dog:read'])]
    private ?string $profilPic = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'clients')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['client:read', 'client:write', 'dog:read'])]
    private ?User $educator = null;

    #[Groups(['client:read'])]
    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Dog::class, orphanRemoval: true)]
    private Collection $dogs;

    public function __construct()
    {
        $this->dogs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

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

    public function getProfilPic(): ?string
    {
        return $this->profilPic;
    }

    public function setProfilPic(?string $profilPic): static
    {
        $this->profilPic = $profilPic;

        return $this;
    }

    public function getEducator(): ?User
    {
        return $this->educator;
    }

    public function setEducator(?User $educator): static
    {
        $this->educator = $educator;

        return $this;
    }

    public function getDogs(): Collection
    {
        return $this->dogs;
    }

    public function addDog(Dog $dog): static
    {
        if (!$this->dogs->contains($dog)) {
            $this->dogs->add($dog);
            $dog->setOwner($this);
        }

        return $this;
    }

    public function removeDog(Dog $dog): static
    {
        if ($this->dogs->removeElement($dog)) {
            if ($dog->getOwner() === $this) {
                $dog->setOwner(null);
            }
        }

        return $this;
    }

}
