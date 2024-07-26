<?php

namespace App\Entity;

use App\Enum\TaskStatus;
use App\Repository\TaskRepository;
use App\Validator as TaskAssert;
use DateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: 'You must enter a title.',
    )]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(
        message: 'You must enter a content.',
    )]
    private string $content;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private User $user;

    #[ORM\Column(type: 'datetime')]
    #[TaskAssert\DeadlineInFuture]
    private DateTime $deadline;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private DateTime $deletedAt;

    #[ORM\Column(type: 'string', enumType: TaskStatus::class)]
    private ?TaskStatus $status = TaskStatus::TODO;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->deadline = new DateTime();
        $this->deadline->setTime(0, 0);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }


    public function getUser(): ?User
    {
        return $this->user ?? null;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDeadline(): DateTime
    {
        return $this->deadline;
    }

    /**
     * @param DateTime $deadline
     */
    public function setDeadline(DateTime $deadline): void
    {
        $this->deadline = $deadline->setTime(0, 0);
    }

    /**
     * @return DateTime
     */
    public function getDeletedAt(): DateTime
    {
        return $this->deletedAt;
    }

    /**
     * @param DateTime $deletedAt
     */
    public function setDeletedAt(DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function getStatus(): ?TaskStatus
    {
        return $this->status;
    }

    public function setStatus(?TaskStatus $status): void
    {
        $this->status = $status;
    }
}
