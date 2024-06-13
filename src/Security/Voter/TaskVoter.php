<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    public const EDIT = 'TASK_EDIT';
    public const DELETE = 'TASK_DELETE';

    public function supports(string $attribute, $subject): bool
    {
        // Make sure this voter supports the attributes and the $subject is a Task object
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Get the current user from the security token
        $user = $token->getUser();

        // Check if the user is connected
        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::EDIT, self::DELETE => $this->canEdit($subject, $user),
            default => throw new LogicException('This code should not be reached!')
        };
    }

    private function canEdit(Task $task, User $user): bool
    {
        // If the connected user has ROLE_ADMIN than can do everything
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }

        // Check if the user is the creator of the task if it's not an ADMIN
        if ($task->getUser() && $user === $task->getUser()) {
            return true;
        }

        return false;
    }
}
