<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Validator\TreasuresAllowedOwnerChange;
use Collator;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    normalizationContext: ['groups' => ['user:read']],
    denormalizationContext: ['groups' => ['user:write']],
    security: 'is_granted("ROLE_USER")',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            security: 'is_granted("PUBLIC_ACCESS")',
            validationContext: ['groups' => ['Default', 'postValidation']],
        ),
        new Put(
            security: 'is_granted("ROLE_USER_EDIT")'),
        new Patch(
            security: 'is_granted("ROLE_USER_EDIT")'),
        new Delete(),
    ],
)]
#[ApiResource(
    uriTemplate: '/treasures/{treasure_id}/owner.{_format}',
    operations: [new Get()],
    uriVariables: [
        'treasure_id' => new Link(
            fromProperty: 'owner',
            fromClass: DragonTreasure::class,
        ),
    ],
    normalizationContext: ['groups' => ['user:read']],
    security: 'is_granted("ROLE_USER")',
)]
#[ApiFilter(PropertyFilter::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['username'], message: 'It looks like another dragon took your username. ROAR!')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    /* Scopes given during API authentication */
    private ?array $accessTokenScopes = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Groups(['user:write'])]
    #[SerializedName('password')]
    #[Assert\NotBlank(groups: ['postValidation'])]
    private ?string $plainPassword = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['user:read', 'user:write', 'treasure:item:get', 'treasure:write'])]
    #[Assert\NotBlank]
    private ?string $username = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: DragonTreasure::class, cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['user:write'])]
    #[Assert\Valid]
    #[TreasuresAllowedOwnerChange()]
    private Collection $dragonTreasures;

    #[ORM\OneToMany(mappedBy: 'ownedBy', targetEntity: ApiToken::class)]
    private Collection $apiTokens;

    public function __construct()
    {
        $this->dragonTreasures = new ArrayCollection();
        $this->apiTokens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        if($this->accessTokenScopes == null){
            // logged in via the full user mechanism
            $roles = $this->roles;
            $roles[] = 'ROLE_FULL_USER';
        }else{
            $roles = $this->accessTokenScopes;
        }
        
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, DragonTreasure>
     */
    public function getDragonTreasures(): Collection
    {
        return $this->dragonTreasures;
    }

    #[Groups(['user:read'])]
    #[SerializedName('dragonTreasures')]
    public function getPublishedDragonTreasures(): Collection
    {
        return $this->dragonTreasures->filter(static function(DragonTreasure $treasure){
            return $treasure->getIsPublished();
        });
    }

    public function addDragonTreasure(DragonTreasure $treasure): self
    {
        if (!$this->dragonTreasures->contains($treasure)) {
            $this->dragonTreasures->add($treasure);
            $treasure->setOwner($this);
        }

        return $this;
    }

    public function removeDragonTreasure(DragonTreasure $treasure): self
    {
        if ($this->dragonTreasures->removeElement($treasure)) {
            // set the owning side to null (unless already changed)
            if ($treasure->getOwner() === $this) {
                $treasure->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ApiToken>
     */
    public function getApiTokens(): Collection
    {
        return $this->apiTokens;
    }

    public function addApiToken(ApiToken $apiToken): static
    {
        if (!$this->apiTokens->contains($apiToken)) {
            $this->apiTokens->add($apiToken);
            $apiToken->setOwnedBy($this);
        }

        return $this;
    }

    public function removeApiToken(ApiToken $apiToken): static
    {
        if ($this->apiTokens->removeElement($apiToken)) {
            // set the owning side to null (unless already changed)
            if ($apiToken->getOwnedBy() === $this) {
                $apiToken->setOwnedBy(null);
            }
        }

        return $this;
    }

    public function getValidTokenStrings(): array{
        return $this->getApiTokens()
            ->filter(fn (ApiToken $token) => $token->isValid())
            ->map(fn (ApiToken $token) => $token->getToken())
            ->toArray()
        ;
    }

    public function markAsTokenScopes(array $scopes) {
        $this->accessTokenScopes = $scopes;
    }

    public function setPlainPassword(string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

}
