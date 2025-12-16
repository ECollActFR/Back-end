<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\ClientAccountRepository;
use App\State\Provider\ClientAccountProvider;
use App\State\Processor\ClientAccountProcessor;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ClientAccountRepository::class)]
#[ApiResource(
    provider: ClientAccountProvider::class,
    processor: ClientAccountProcessor::class,
    operations: [
        new Get(security: "is_granted('view', object)"),
        new GetCollection(security: "is_granted('ROLE_USER')"),
        new Post(security: "is_granted('create', object)"),
        new Patch(security: "is_granted('edit', object)"),
        new Delete(security: "is_granted('delete', object)"),
    ]
)]
class ClientAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['client_account:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['client_account:read', 'client_account:write', 'user:read'])]
    private ?string $companyName = null;

    #[ORM\Column(length: 14, nullable: true)]
    #[Groups(['client_account:read', 'client_account:write'])]
    private ?string $siret = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['client_account:read', 'client_account:write'])]
    private ?string $address = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['client_account:read', 'client_account:write'])]
    private ?string $city = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Groups(['client_account:read', 'client_account:write'])]
    private ?string $postalCode = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['client_account:read', 'client_account:write'])]
    private ?string $country = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['client_account:read', 'client_account:write'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['client_account:read', 'client_account:write'])]
    private ?string $contactEmail = null;

    #[ORM\Column]
    #[Groups(['client_account:read'])]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column]
    #[Groups(['client_account:read'])]
    private ?bool $isActive = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'clientAccount', orphanRemoval: true)]
    private Collection $users;



    public function __construct(?string $companyName = null)
    {
        $this->companyName = $companyName;
        $this->createdAt = Carbon::now();
        $this->users = new ArrayCollection();

        $this->isActive = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): static
    {
        $this->siret = $siret;

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

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): static
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setClientAccount($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getClientAccount() === $this) {
                $user->setClientAccount(null);
            }
        }

        return $this;
    }


}