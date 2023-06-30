<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use http\Encoding\Stream;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60, unique: true)]
    #[Assert\NotBlank(
        message: 'You must enter a username.',
    )]
    #[Groups(
        [
            'getUsers'
        ])
    ]
    private string $username;

    #[ORM\Column(length: 60, unique: true)]
    #[Assert\NotBlank(
        message: 'You must enter a valid email.'
    )]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
        mode: 'html5'
    )]
    #[Groups(
        [
            'getUsers'
        ])
    ]
    private string $email;

    #[ORM\Column]
    #[Groups(
        [
            'getUsers'
        ])
    ]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 64)]
    #[Assert\NotBlank(
        message: 'You must enter a password.',
    )]
//    #[Assert\PasswordStrength([
//        'minScore' => PasswordStrength::STRENGTH_VERY_WEAK,
//    ])]
    private string $password;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
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
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

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
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
