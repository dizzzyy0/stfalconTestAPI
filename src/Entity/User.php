<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Symfony\Component\Uid\Uuid;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[DiscriminatorColumn(name: 'discriminator', type: 'string')]
#[DiscriminatorMap([
    "agent" => Agent::class,
    "customer" => Customer::class,
    "admin" => Admin::class,
])]
abstract class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_AGENT = 'agent';
    public const ROLE_CUSTOMER = 'customer';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[Groups(["list", "details"])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(["list", "details"])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(["list", "details"])]
    private array $roles = [self::ROLE_CUSTOMER];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    #[Groups(["list", "details"])]
    private ?string $phone = null;

    #[ORM\Column]
    #[Groups(["list", "details"])]
    private ?string $name = null;

    #[ORM\Column]
    private bool $isBlocked = false;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?Uuid
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
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $symfonyRoles = [];

        foreach ($this->roles as $role) {
            $symfonyRoles[] = 'ROLE_' . strtoupper($role);
        }

        // guarantee every user at least has ROLE_CUSTOMER
        $symfonyRoles[] = 'ROLE_CUSTOMER';

        return array_unique($symfonyRoles);
    }

    /**
     * Get the raw roles stored in the database (lowercase strings)
     */
    public function getSimpleRoles(): array
    {
        return $this->roles;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;
        return $this;
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

    public function isBlocked(): ?bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): static
    {
        $this->isBlocked = $isBlocked;
        return $this;
    }
}
