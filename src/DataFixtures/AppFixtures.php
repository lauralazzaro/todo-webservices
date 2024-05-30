<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    // @codeCoverageIgnoreStart
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getGroups(): array
    {
        return ['dev','test'];
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

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@email.com');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            '123'
        );
        $admin->setPassword($hashedPassword);
        $admin->setIsPasswordGenerated(false);
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $manager->persist($admin);
        $manager->flush();

        for ($i = 1; $i <= 5; $i++) {
            $task = new Task();
            $task->setTitle('Task without user n. ' . $i);
            $task->setContent('This task has no user');
            $manager->persist($task);
            $manager->flush();
        }

        for ($i = 1; $i <= 5; $i++) {
            $task = new Task();
            $task->setTitle('Task from user n. ' . $i);
            $task->setContent('This task was created by a user');
            $task->setUser($user);
            $manager->persist($task);
            $manager->flush();
        }

        for ($i = 1; $i <= 5; $i++) {
            $task = new Task();
            $task->setTitle('Task from admin n. ' . $i);
            $task->setContent('This task was created by an admin');
            $task->setUser($admin);
            $manager->persist($task);
            $manager->flush();
        }
    }
    // @codeCoverageIgnoreEnd
}
