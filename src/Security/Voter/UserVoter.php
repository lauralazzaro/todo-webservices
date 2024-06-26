<?php

namespace App\Security\Voter;

use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const EDIT = 'USER_EDIT';
    public const READ = 'USER_READ';

    public function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::READ])
            && is_int($subject);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Get the current user from the security token
        $loggedUser = $token->getUser();
        $id = $subject;

        // @codeCoverageIgnoreStart
        // Check if the user is connected
        if (!$loggedUser instanceof User) {
            return false;
        }
        // @codeCoverageIgnoreEnd

        return match ($attribute) {
            self::EDIT, self::READ => $this->canEdit($loggedUser, $id),
            default => throw new LogicException('This code should not be reached!')
        };
    }

    private function canEdit($loggedUser, $id): bool
    {
        if ($loggedUser->getId() === $id) {
            return true;
        }

        return false;
    }
}
