<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskVoter extends Voter
{
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    public function supports(string $attribute, $subject): bool
    {
        // Make sure this voter supports the "edit" attribute and the $subject is a Task object
        return in_array($attribute, [self::EDIT, self::DELETE]) && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Get the current user from the security token
        $user = $token->getUser();

        // If the user is not logged in, they cannot edit the task
        if (!$user instanceof User) {
            return false;
        }

        // Check if the user is the creator of the task
        if ($subject->getUser()) {
            return $user === $subject->getUser();
        }

        // if the task has user = NULL it can be edited or deleted only by ADMINS
        if (!$subject->getUser()) {
            return in_array('ROLE_ADMIN', $user->getRoles());
        }
    }
}
