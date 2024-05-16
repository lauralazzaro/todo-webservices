<?php

namespace App\Helper;

use App\Entity\User;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserHelper
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * when an admin creates a user, the username will be the mail and after that the user can modify it
     * first the user is not validated
     *
     * @param User $user
     * @return array ['user' => User Object, 'plainPassword' => generated password]
     * @throws Exception
     */
    public function initUserData(
        User $user
    ): array {
        $user->setUsername($user->getEmail());
        $user->setIsPasswordGenerated(true);

        $random = bin2hex(random_bytes(3));

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $random
        );

        $user->setPassword($hashedPassword);

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        }

        return ['user' => $user, 'plainPassword' => $random];
    }


    /**
     * Return the user with hashed password
     *
     * @param User $user
     * @return User
     */
    public function updatePassword(
        User $user
    ): User {
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );

        $user->setPassword($hashedPassword);
        $user->setIsPasswordGenerated(false);

        return $user;
    }
}
