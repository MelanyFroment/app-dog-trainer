<?php

namespace App\Entity;

use App\Repository\DogRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\User;

#[ORM\Entity(repositoryClass: DogRepository::class)]
class Dog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['dog:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['dog:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['dog:read'])]
    private ?string $breed = null;

    #[ORM\Column]
    #[Groups(['dog:read'])]
    private ?int $age = null;

    #[ORM\Column(length: 255)]
    #[Groups(['dog:read'])]
    private ?string $sex = null;

    #[ORM\Column]
    #[Groups(['dog:read'])]
    private ?float $weight = null;

    #[ORM\Column]
    #[Groups(['dog:read'])]
    private ?bool $vaccinated = null;


    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'dogs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['dog:read'])]
    private ?Client $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBreed(): ?string
    {
        return $this->breed;
    }

    public function setBreed(string $breed): static
    {
        $this->breed = $breed;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(string $sex): static
    {
        $this->sex = $sex;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function isVaccinated(): ?bool
    {
        return $this->vaccinated;
    }

    public function setVaccinated(bool $vaccinated): static
    {
        $this->vaccinated = $vaccinated;

        return $this;
    }

    public function getOwner(): ?Client
    {
        return $this->owner;
    }

    public function setOwner(?Client $owner): static
    {
        $this->owner = $owner;
        return $this;
    }

}
