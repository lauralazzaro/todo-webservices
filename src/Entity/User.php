<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["task_list"])]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, unique: true, nullable: false)]
    #[Assert\NotBlank(message: 'You must enter a username.')]
    #[Groups(["task_list"])]
    private string $username;

    #[ORM\Column(type: "string", length: 255, unique: true, nullable: false)]
    #[Assert\Email(message: 'Invalid Email Format')]
    #[Assert\NotBlank(message: 'You must enter a valid email.')]
    #[Groups(["task_list"])]
    private string $email;

    #[ORM\Column]
    #[Groups(["task_list"])]
    private array $roles = [];

    #[ORM\Column(type: "string", length: 255)]
    private string $password;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Task::class)]
    private Collection $tasks;

    #[ORM\Column(type: "boolean")]
    private bool $isPasswordGenerated = true;

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
        return $this->username;
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

    /**
     * @codeCoverageIgnore
     */
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }
}
