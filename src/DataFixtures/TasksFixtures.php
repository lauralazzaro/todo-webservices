<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Date;

class TasksFixtures extends Fixture implements FixtureGroupInterface
{
    // @codeCoverageIgnoreStart
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getGroups(): array
    {
        return ['dev'];
    }

    public function load(
        ObjectManager $manager
    ): void {
        for ($i = 1; $i <= 5; $i++) {
            $task = new Task();
            $task->setTitle('Task without user n. ' . $i);
            $task->setContent('This task has no user');
            $task->setDeadline(new \DateTime('today'));
            $manager->persist($task);
            $manager->flush();
        }

        for ($i = 1; $i <= 5; $i++) {
            $task = new Task();
            $task->setTitle('Task without user n. ' . $i);
            $task->setContent('This task has no user');
            $task->setDeadline(new \DateTime('tomorrow'));
            $manager->persist($task);
            $manager->flush();
        }

        $date = new \DateTime('now');
        $date->modify('+5 days');

        for ($i = 1; $i <= 5; $i++) {
            $task = new Task();
            $task->setTitle('Task without user n. ' . $i);
            $task->setContent('This task has no user');
            $task->setDeadline($date);
            $manager->persist($task);
            $manager->flush();
        }

    }
    // @codeCoverageIgnoreEnd
}
