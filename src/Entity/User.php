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
    #[Assert\NotBlank(
        message: 'You must enter a valid username.'
    )]
    private string $username;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    #[Assert\NotBlank(
        message: 'You must enter a valid email.'
    )]
    private string $email;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(type: "string", length: 255)]
    private string $password;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Task::class)]
    private Collection $tasks;

    #[ORM\Column(type: "boolean")]
    private string $isPasswordGenerated;

    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeImmutable $generatedPasswordValidity;

    #[ORM\Column(type: "boolean")]
    private bool $isValidated;

    public function __construct()
    {
        $this->isValidated = false;
        $this->isPasswordGenerated = false;
        $this->generatedPasswordValidity = new DateTimeImmutable();
    }

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
     * @param string $username
     */
    public function setUsername(string $username): void
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
     * @return DateTimeImmutable|null
     */
    public function getGeneratedPasswordValidity(): ?DateTimeImmutable
    {
        return $this->generatedPasswordValidity;
    }

    public function setGeneratedPasswordValidity(): void
    {
        $datetimeNow = new DateTimeImmutable();
        $expirationDate = $datetimeNow->modify('+48 hours');

        $this->generatedPasswordValidity = $expirationDate;
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

    /**
     * @return bool
     */
    public function isPasswordGenerated(): bool
    {
        return $this->isPasswordGenerated;
    }

    /**
     * @param bool $isPasswordGenerated
     */
    public function setIsPasswordGenerated(bool $isPasswordGenerated): void
    {
        $this->isPasswordGenerated = $isPasswordGenerated;
    }



    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
