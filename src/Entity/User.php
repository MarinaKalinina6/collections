<?php

namespace App\Entity;

use App\Enum\UserStatus;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    private Uuid $id;

    #[ORM\Column(type: Types::STRING, length: 180, unique: true)]
    private string $username;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $password;

    #[ORM\Column(type: 'json')]
    private array $roles;

    #[ORM\Column( enumType: UserStatus::class)]
    private UserStatus $status;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly \DateTimeImmutable $addedAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $lastSignInAt;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->password = null;
        $this->roles = ['ROLE_USER'];
        $this->addedAt = new \DateTimeImmutable();
        $this->lastSignInAt = null;
        $this->status = UserStatus::AwaitingActivator;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function getLastSignInAt(): ?\DateTimeImmutable
    {
        return $this->lastSignInAt;
    }

    public function setLastSignInAt(?\DateTimeImmutable $lastSignInAt): self
    {
        $this->lastSignInAt = $lastSignInAt;

        return $this;
    }

    public function markSignedIn(): self
    {
        $this->lastSignInAt = new \DateTimeImmutable();

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function setStatus(UserStatus $status):self
    {
        $this->status = $status;
        return $this;

    }

}
