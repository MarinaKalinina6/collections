<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Symfony\Component\Uid\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly \DateTimeImmutable $addedAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $lastSignInAt;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isActive;

    public function __construct(string $username)
    {
        $this->id = Uuid::v7();
        $this->username = $username;
        $this->password = null;
        $this->roles = ['ROLE_USER'];
        $this->addedAt = new \DateTimeImmutable();
        $this->lastSignInAt = null;
        $this->isActive = true;
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
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

    public function markSignedIn(): self
    {
        $this->lastSignInAt = new \DateTimeImmutable();

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function block(): self
    {
        $this->isActive = false;
        return $this;
    }

    public function unblock(): self
    {
        $this->isActive = true;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
    }
}
