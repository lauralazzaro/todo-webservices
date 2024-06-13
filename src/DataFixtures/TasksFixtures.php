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
            $task->setTitle('task deadline tomorrow n. ' . $i);
            $task->setContent('Deadline tomorrow');
            $task->setDeadline(new \DateTime('tomorrow'));
            $manager->persist($task);
            $manager->flush();
        }

        for ($i = 1; $i <= 5; $i++) {
            $task = new Task();
            $task->setTitle('task far away n. ' . $i);
            $task->setContent('deadline in 5 days');
            $task->setDeadline(new \DateTime('+5 days'));
            $manager->persist($task);
            $manager->flush();
        }

        for ($i = 1; $i <= 5; $i++) {
            $task = new Task();
            $task->setTitle('task overdue n. ' . $i);
            $task->setContent('This task is overdue');
            $task->setDeadline(new \DateTime('-5 days'));
            $manager->persist($task);
            $manager->flush();
        }
    }
    // @codeCoverageIgnoreEnd
}
