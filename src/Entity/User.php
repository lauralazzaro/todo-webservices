<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private string $username;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    #[Assert\NotBlank(
        message: 'You must enter a valid email.'
    )]
    private string $email;
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Task::class)]
    private Collection $tasks;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $verificationToken = null;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?DateTimeImmutable $verificationTokenExpirationDate = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private bool $isValidated;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
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
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function isEmailValid(string $email): bool
    {
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        return preg_match($pattern, $email) === 1
            ?: "The email $email is not a valid email.";
    }

    /**
     * @return string|null
     */
    public function getVerificationToken(): ?string
    {
        return $this->verificationToken;
    }

    /**
     * @param string|null $verificationToken
     */
    public function setVerificationToken(?string $verificationToken): void
    {
        $this->verificationToken = $verificationToken;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getVerificationTokenExpirationDate(): ?DateTimeImmutable
    {
        return $this->verificationTokenExpirationDate;
    }

    public function setVerificationTokenExpirationDate(): void
    {
        $datetimeNow = new DateTimeImmutable();
        $expirationDate = $datetimeNow->modify('+48 hours');

        $this->verificationTokenExpirationDate = $expirationDate;
    }

    /**
     * @return bool
     */
    public function isValidated(): bool
    {
        return $this->isValidated;
    }

    /**
     * @param bool $isValidated
     */
    public function setIsValidated(bool $isValidated): void
    {
        $this->isValidated = $isValidated;
    }


    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
