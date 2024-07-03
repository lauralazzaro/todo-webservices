<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestingFixtures extends Fixture implements FixtureGroupInterface
{
    // @codeCoverageIgnoreStart
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function load(
        ObjectManager $manager
    ): void {
        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $user->setUsername("user$i");
            $user->setEmail("user$i@email.com");
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                '123'
            );
            $user->setPassword($hashedPassword);
            $user->setIsPasswordGenerated(false);
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $manager->flush();

            for ($y = 1; $y <= 5; $y++) {
                $task = new Task();
                $task->setTitle("Task $y for user $i");
                $task->setContent("Content for task $y for user $i");
                $task->setUser($user);
                $manager->persist($task);
            }
        }

        for ($i = 1; $i <= 5; $i++) {
            $admin = new User();
            $admin->setUsername("admin$i");
            $admin->setEmail("admin$i@email.com");
            $hashedPassword = $this->passwordHasher->hashPassword(
                $admin,
                '123'
            );
            $admin->setPassword($hashedPassword);
            $admin->setIsPasswordGenerated(false);
            $admin->setRoles(['ROLE_ADMIN']);
            $manager->persist($admin);
            $manager->flush();

            for ($y = 1; $y <= 5; $y++) {
                $task = new Task();
                $task->setTitle("Task $y for admin $i");
                $task->setContent("Content for task $y for admin $i");
                $task->setUser($admin);
                $manager->persist($task);
            }
        }
    }
    // @codeCoverageIgnoreEnd
}
