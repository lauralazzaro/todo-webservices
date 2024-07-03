<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    // @codeCoverageIgnoreStart
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getGroups(): array
    {
        return ['dev', 'test'];
    }

    public function load(
        ObjectManager $manager
    ): void {
        $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@email.com');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            '123'
        );
        $user->setPassword($hashedPassword);
        $user->setIsPasswordGenerated(false);
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);
        $manager->flush();

        $this->addReference('user', $user);

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@email.com');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            '123'
        );
        $admin->setPassword($hashedPassword);
        $admin->setIsPasswordGenerated(false);
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $manager->flush();

        $this->addReference('admin', $admin);
    }
    // @codeCoverageIgnoreEnd
}
