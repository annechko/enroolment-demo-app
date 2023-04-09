<?php

declare(strict_types=1);

namespace App\Domain\Admin\Entity;

use App\Domain\School\Common\RoleEnum;
use App\Repository\AdminUserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminUserRepository::class)]
#[ORM\Table(name: 'admin_user')]
#[ORM\UniqueConstraint('uk_admin_user_email', ['email'])]
class AdminUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true, nullable: false)]
    private string $email;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(nullable: false)]
    private string $passwordHash;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $surname = null;

    public function __construct(
        string $email,
        string $passwordHash,
    ) {
        $this->email = $email;
        $this->passwordHash = $passwordHash;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = RoleEnum::USER->value;
        $roles[] = RoleEnum::ADMIN_USER->value;

        return array_unique($roles);
    }

    public function getPassword(): string
    {
        return $this->passwordHash;
    }

    public function upgradePasswordHash(string $password): self
    {
        $this->passwordHash = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }
}
