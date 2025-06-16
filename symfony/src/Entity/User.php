<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "app_user")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['user:read'])]

    private ?int $id = null;

    #[ORM\Column(type: 'string', unique: true)]
    #[Groups(['user:read'])]
    private string $email;

    #[ORM\Column(type: 'string')]
    #[Groups(['user:read'])]
    private string $password;

    #[ORM\Column(type: 'json')]
    #[Groups(['user:read'])]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    #[Groups(['user:read'])]
    private string $phone;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['user:read'])]
    private \DateTimeImmutable $created_date;


    /**
     * @var Collection<int, Client>
     */
    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'educator', orphanRemoval: true)]
    private Collection $clients;

    public function __construct()
    {
        $this->clients = new ArrayCollection();

    }

    // ------------------ Getters / Setters ------------------ //

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getCreatedDate(): \DateTimeImmutable
    {
        return $this->created_date;
    }

    public function setCreatedDate(\DateTimeImmutable $created_date): self
    {
        $this->created_date = $created_date;
        return $this;
    }

    public function getAllDogs(): array
    {
        $dogs = [];
        foreach ($this->clients as $client) {
            foreach ($client->getDogs() as $dog) {
                $dogs[] = $dog;
            }
        }
        return $dogs;
    }


    // ------------------ UserInterface ------------------ //

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return Collection<int, Client>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): static
    {
        if (!$this->clients->contains($client)) {
            $this->clients->add($client);
            $client->setEducator($this);
        }

        return $this;
    }

    public function removeClient(Client $client): static
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getEducator() === $this) {
                $client->setEducator(null);
            }
        }

        return $this;
    }
}
