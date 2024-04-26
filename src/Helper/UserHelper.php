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
     * set the expiration of the token at +48 hours
     * generate a random token
     *
     * @param User $user
     * @return User
     * @throws Exception
     */
    public function initUserData(
        User $user
    ): User {
        $user->setUsername($user->getEmail());
        $user->setIsValidated(false);
        $user->setGeneratedPasswordValidity();
        $user->setIsPasswordGenerated(true);

        $random = $this->randomPassword();
        $user->setPassword($random);

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        } else {
            $user->setRoles(['ROLE_USER']);
        }

        return $user;
    }

    private function randomPassword(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < 6; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    /**
     * Verify that the password is less than 48h old
     *
     * @param \DateTimeImmutable $passwordExpirationDate
     * @return bool
     */
    public function isPasswordStillValid(\DateTimeImmutable $passwordExpirationDate): bool
    {
        $now = new \DateTimeImmutable();

        return $now->diff($passwordExpirationDate)->h < 48;
    }

    /**
     * Return the user with hashed password
     *
     * @param User $user
     * @param UserPasswordHasherInterface $passwordHasher
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

        return $user;
    }
}
